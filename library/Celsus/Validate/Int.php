<?php

class Celsus_Validate_Int extends Zend_Validate_Int {
	
	/**
	 * Returns code snippet used for client-side validation.
	 * 
	 * @param string $name The name of the element.
	 * @return array The constraint and message.
	 */
	public function getClientSideValidation($name) {
		return array("/^\d+$/.test(\$F('$name'))", 'Please enter a valid number');
	}	
}
?>