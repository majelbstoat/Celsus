<?php

class Celsus_Validate_Empty extends Zend_Validate_Abstract {

	const NOT_EMPTY = 'notEmpty';

	/**
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::NOT_EMPTY => "Value is not empty, but an empty value is required"
	);

	/**
	 * Defined by Zend_Validate_Interface
	 *
	 * Returns true if and only if $value is an empty value.
	 *
	 * @param  string $value
	 * @return boolean
	 */
	public function isValid($value) {
		$valueString = (string) $value;

		$this->_setValue($valueString);

		if (!empty($value)) {
			$this->_error(self::NOT_EMPTY);
			return false;
		}

		return true;
	}

	/**
	 * Returns code snippet used for client-side validation.
	 * 
	 * @param string $name The name of the element.
	 * @return array The constraint and message.
	 */
	public function getClientSideValidation($name) {
		return array("!\$F('$name')", "This field must be empty");
	}
}
