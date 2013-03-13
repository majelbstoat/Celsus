<?php

/**
 * Data transfer object which allows for tight control of the fields that can be set.
 *
 * Basically a glorified array with some restrictions.
 *
 * @author majelbstoat
 */
class Celsus_Data_Struct extends Celsus_Data {

	protected static $_marshals = array();

	public function __construct($data = null) {
		if (null !== $data) {
			$this->_importData($data);
		}
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

	public function setFromArray($data) {
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}

	public function getData() {
		$classname = get_class($this);

		$reflection = new ReflectionClass($this);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

		$return = array();
		foreach ($properties as $property) {
			if ($property->class === $classname) {
				$return[$property->name] = $this->{$property->name};
			}
		}
		return $return;
	}

	/**
	 * Merges another Celsus_Data_Struct into this one.
	 *
	 * @param string $field
	 * @param mixed $value
	 */
	public function mergeData(self $new) {
		$data = $new->toArray();
		foreach ($data as $field => $value) {
			$this->$field = $value;
		}
	}

	protected function _importData($data) {

		$importedData = null;

		if (is_array($data)) {
			// Simplified object mode.
			$importedData = $data;
		} elseif (is_object($data)) {
			$imported = false;
			foreach(self::$_marshals as $provided => $marshal) {
				if ($provided == get_class($data)) {
					// We have a provider that can marshal this object.
					$importedData = call_user_func(array($marshal, 'provide'), $data);
					$imported = true;
					break;
				}
			}

			if (!$imported && method_exists($data, "toArray")) {
				// Final fallback approach - comes from a source that can set the data from an array.
				$importedData = $data->toArray();
			}
		}

		if (null === $importedData) {
			throw new Celsus_Exception("Data must be an array, have a providing marshal, or be an object implementing toArray");
		}

		$this->_setData($importedData);

		return $this;
	}

	protected function _setData($data) {
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}

	public function __get($key) {
		// Non-public fields may not be retrieved.
		return null;
	}

	public function __set($key, $value) {
		// Non-public fields may not be set.
	}
}