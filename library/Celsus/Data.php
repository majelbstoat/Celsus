<?php

abstract class Celsus_Data implements Celsus_Data_Interface {

	/**
	 * Prefix of the formatters that can format a Celsus Data Object.  Must be an
	 * array because applications might want to add their own.
	 *
	 * @var array
	 */
	protected $_formatterPrefixes = array(
		'Celsus_Data_Formatter_'
	);

	/**
	 * Outputs information as an array.
	 *
	 * Required by interface.
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->getData();
	}

	/**
	 * Having this method defined directly makes it possible for
	 * Zend_Json::decode() to work natively.
	 *
	 * @return string
	 */
	public function toJson() {
		return $this->_output('Json');
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
			return $this->_output($format);
		}
	}

	/**
	 * Allow echoing of data objects.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->_output('String');
	}

	/**
	 * Prepends a formatter prefix onto the list, so that applications can define (and
	 * optionally overwrite) default formatters.
	 *
	 * @param string $formatterPrefix
	 */
	public function addFormatterPrefix($formatterPrefix) {
		if (!is_string($formatterPrefix)) {
			throw new Celsus_Exception("Formatter prefixes must be a string.");
		}
		array_unshift($this->_formatterPrefixes, $formatterPrefix);
	}

	protected function _output($format) {
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
