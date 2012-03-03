<?php

abstract class Celsus_Data_Abstract implements Celsus_Data_Interface {

	/**
	 * Name of the record in singular form Ucfirst
	 *
	 * @var string
	 */
	protected $_name = null;

	/**
	 * The record data in a non-nested assoc array
	 *
	 * @var mixed
	 */
	protected $_data = array();

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
	 * Records whether data has been changed.
	 *
	 * @var boolean
	 */
	protected $_dirty = array();

	/**
	 * The original, pristine data in the object.
	 *
	 * @var array
	 */
	protected $_originalData = null;

	public function __construct(array $data, string $name) {
		$this->_data = $data;
		$this->_name = $name;
	}

	/**
	 * Returns the name of this object.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Determines whether this object holds data.
	 *
	 * @return boolean
	 */
	public function isEmpty() {
		return empty($this->_data);
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
}
