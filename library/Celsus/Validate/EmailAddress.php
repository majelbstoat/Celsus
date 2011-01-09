<?php

class Celsus_Validate_EmailAddress extends Zend_Validate_EmailAddress {

	/**
	 * Returns code snippet used for client-side validation.
	 *
	 * @param string $name The name of the element.
	 * @return array The constraint and message.
	 */
	public function getClientSideValidation($name) {
		return array("/^[A-Z0-9._%-]+@[A-Z0-9.-]+$/i.test(\$F('$name'))", 'Please enter a valid email address');
	}
}
