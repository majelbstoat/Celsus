<?php
/**
 * Semi-immutable objects that provide a simple standard way for marshalling data
 * in an application.  When data is changed, the object is marked dirty.
 * Each object is subject to a filter, allowing field-level read and write
 * permissions to be set as needed.  Data can be read in any number of forms,
 * as long as a formatter exists for that format.
 *
 */
class Celsus_Data_Object extends Celsus_Data_Abstract {

	/**
	 * The default filter to be used for new objects.
	 *
	 * @var string
	 */
	protected static $_defaultFilter = 'Celsus_Data_Filter_Default';

	/**
	 * The filter to be used for this object.
	 *
	 * @var string
	 */
	protected $_filter = null;

	protected static $_marshals = array();

	/**
	 * The fields that are readable to the current identity.
	 *
	 * @var array
	 */
	protected $_readableFields = null;

	/**
	 * The fields that are writeable to the current identity.
	 *
	 * @var array
	 */
	protected $_writeableFields = null;

	/**
	 * Takes a dataset and wraps it in a semi-immutable object that tracks changes.
	 *
	 * @param mixed $data
	 * @return Celsus_Data_Object
	 */
	public function __construct($data) {
		$this->_filter = static::$_defaultFilter;
		$this->_setData($data);
	}

	public static function addMarshal($marshal) {
		if (!in_array('Celsus_Data_Marshal_Interface', class_implements($marshal, true))) {
			throw new Celsus_Exception("$marshal must implement Celsus_Data_Marshal_Interface.");
		}
		$marshalledClass = call_user_func(array($marshal, 'provides'));
		self::$_marshals[$marshalledClass] = $marshal;
	}

	public static function setMarshals(array $marshals) {
		self::$_marshals = array();
		foreach($marshals as $marshal) {
			self::addMarshal($marshal);
		}
	}

	protected function _setData($data) {
		if (is_array($data)) {
			// Simplified object mode.
			$this->_data = $data;
		} elseif (is_object($data)) {
			$provided = false;
			foreach(self::$_marshals as $provided => $marshal) {
				if ($provided == get_class($data)) {
					// We have a provider that can marshal this object.
					$this->_data = call_user_func(array($marshal, 'provide'), $data);
					$provided = true;
					break;
				}
			}
			if (!$provided && method_exists($data, "toArray")) {
				// Final fallback approach - comes from a source that can set the data from an array.
				$this->_data = $data->toArray();
			}
		}

		if (null === $this->_data) {
			throw new Celsus_Exception("Data must be an array, have a providing marshal, or be an object implementing toArray");
		}

		$this->_readableFields = null;
		$this->_writeableFields = null;
		return $this;
	}

	/**
	 * Sets the filter to be used for this data object.
	 *
	 * @param string $filter
	 * @return Celsus_Data_Object
	 */
	public function setFilter($filter) {
		if (!in_array('Celsus_Data_Filter_Interface', class_implements($filter, true))) {
			throw new Celsus_Exception("$filter must implement Celsus_Data_Filter_Interface.");
		}
		$this->_filter = $filter;
		$this->_readableFields = null;
		$this->_writeableFields = null;
		return $this;
	}

	/**
	 * Sets the default filter to be used for all data objects.
	 *
	 * @param string $filter
	 */
	public static function setDefaultFilter($filter) {
		if (!in_array('Celsus_Data_Filter_Interface', class_implements($filter, true))) {
			throw new Celsus_Exception("$filter must implement Celsus_Data_Filter_Interface.");
		}
		self::$_defaultFilter = $filter;
	}

	/**
	 * Gets the filter in use for this object.
	 */
	public function getFilter() {
		return $this->_filter;
	}

	/**
	 * Gets the filter in use for this object.
	 */
	public static function getDefaultFilter() {
		return self::$_defaultFilter;
	}

	/**
	 * Uses the filter to determine whether the supplied field is readable to the
	 * current identity.
	 *
	 * @param string $field
	 * @return boolean
	 */
	public function isReadable($field) {
		if (null === $this->_readableFields) {
			// We haven't filtered the data yet, so filter it now.
			$this->_determineReadableFields();
		}
		return in_array($field, $this->_readableFields);
	}

	/**
	 * Uses the filter to determine whether the supplied field is writable to the
	 * current identity.
	 *
	 * @param string $field
	 * @return boolean
	 */
	public function isWriteable($field) {
		if (null === $this->_writeableFields) {
			// We haven't filtered the data yet, so filter it now.
			$this->_determineWriteableFields();
		}
		return in_array($field, $this->_writeableFields);
	}

	/**
	 * Sets the array of readable fields, using the filter.
	 */
	protected function _determineReadableFields() {
		$this->_readableFields = call_user_func(array(
			$this->_filter, 'filterReadable'
		), $this, array_keys($this->_data));
	}

	/**
	 * Sets the array of writeable fields, using the filter.
	 */
	protected function _determineWriteableFields() {
		$this->_writeableFields = call_user_func(array(
			$this->_filter, 'filterWriteable'
		), $this, array_keys($this->_data));
	}

	/**
	 * Allows simple, secure access to the data.
	 *
	 * @param string $field
	 */
	public function __get($field) {
		if (!array_key_exists($field, $this->_data)) {
			throw new Celsus_Exception("Unknown field $field in $this->_name Celsus Data Object.");
		}

		if (!$this->isReadable($field)) {
			// This field isn't readable in the current security model, so don't return it.
			return null;
		}

		return $this->_data[$field];
	}

	/**
	 * Allows simple, secure access to changing data, along with marking of the
	 * object as dirty.
	 *
	 * @param string $field
	 * @param mixed $value
	 */
	public function __set($field, $value) {

		if (!array_key_exists($field, $this->_data)) {
			throw new Celsus_Exception("Unknown field $field in $this->_name Celsus Data Object.");
		}

		// If we can't write to this field, do nothing.
		if (!$this->isWriteable($field)) {
			return false;
		}

		// If the data hasn't changed, do nothing.
		if ($this->_data[$field] === $value) {
			return true;
		}

		$this->_dirty = true;
		if (null === $this->_originalData) {
			// Make a copy of the original data, in case we need access to it later.
			$this->_originalData = $this->_data;
		}

		// Set the value.
		$this->_data[$field] = $value;
		return true;
	}

	/**
	 * Merges another Celsus_Data_Object into this one.  The objects must both
	 * be of the same name.
	 *
	 * @param string $field
	 * @param mixed $value
	 */
	public function mergeData(Celsus_Data_Object $new) {
		$name = $new->getName();
		$data = $new->toArray();
		if (isset($data[$name])) {
			foreach ($data[$name] as $field => $value) {
				$this->$field = $value;
			}
		}
	}

	/**
	 * Returns all the data, subject to the current identity's permissions.
	 *
	 * @return array;
	 */
	public function getData() {
		if (null === $this->_readableFields) {
			$this->_determineReadableFields();
		}
		return array_intersect_key($this->_data, array_flip($this->_readableFields));
	}

	/**
	 * Convenience method, in case people expect it.
	 */
	public function toArray() {
		return $this->getData();
	}

	/**
	 * Magic function implements rendering.
	 *
	 * @param string $name
	 * @param arguments $arguments
	 * @see Celsus_Data_Formatter_Interface
	 */
	public function __call($method, $arguments) {
		if ('to' == substr($method, 0, 2)) {
			$format = substr($method, 2);

			// Iterate all the formatter prefixes and determine whether we can render.
			foreach ($this->_formatterPrefixes as $prefix) {
				$class = $prefix . $format;
				if (!class_exists($class, true)) {
					// Class doesn't exist with this prefix.
					continue;
				}

				if (!in_array('Celsus_Data_Formatter_Interface', class_implements($class))) {
					// Class exists, but doesn't implement the correct interface.
					continue;
				}

				// Interface dictates that we can call format() on it.
				return call_user_func(array($class, 'format'), $this);
			}

			// No formatters exist for the requested type.
			throw new Celsus_Exception("No formatter exists for $class that implements Celsus_Data_Formatter_Interface.");
		}
	}

	public function __isset($field) {
		return isset($this->_data[$field]);
	}

}