<?php
/**
 * Semi-immutable objects that provide a simple standard way for marshalling data
 * in an application.  When data is changed, the object is marked dirty.
 *
 * Each object is subject to a filter, allowing field-level read and write
 * permissions to be set as needed.  Data can be read in any number of forms,
 * as long as a formatter exists for that format.
 */
class Celsus_Data_Object extends Celsus_Data_Struct {

	/**
	 * The object data in a non-nested assoc array
	 *
	 * @var mixed
	 */
	protected $_data = null;

	/**
	 * The default filter to be used for new objects.
	 *
	 * @var string
	 */
	protected static $_defaultFilter = 'Celsus_Data_Filter_Default';

	/**
	 * Records whether data has been changed.
	 *
	 * @var array
	 */
	protected $_dirty = array();

	/**
	 * The filter to be used for this object.
	 *
	 * @var string
	 */
	protected $_filter = null;

	/**
	 * The original, pristine data in the object.
	 *
	 * @var array
	 */
	protected $_originalData = null;

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
		$this->_importData($data);
	}

	/**
	 * Determines whether this object holds data.
	 *
	 * @return boolean
	 */
	public function isEmpty() {
		return empty($this->_data);
	}

	protected function _importData($data) {

		parent::_importData($data);

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

	protected function _setData($data) {
		$this->_data = $data;
	}

	/**
	 * Allows simple, secure access to the data.
	 *
	 * @param string $field
	 */
	public function __get($field) {
		if (!array_key_exists($field, $this->_data)) {
			return null;
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
			Celsus_Log::error("Unknown field $field in $this->_name Celsus Data Object");
			return false;
		}

		// If we can't write to this field, do nothing.
		if (!$this->isWriteable($field)) {
			return false;
		}

		// If the data hasn't changed, do nothing.
		if ($this->_data[$field] === $value) {
			return false;
		}

		$this->_dirty[] = $field;
		if (null === $this->_originalData) {
			// Make a copy of the original data, in case we need access to it later.
			$this->_originalData = $this->_data;
		}

		// Set the value.
		$this->_data[$field] = $value;
		return true;
	}

	public function __isset($field) {
		return isset($this->_data[$field]);
	}

}