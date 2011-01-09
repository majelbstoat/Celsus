<?php

class Celsus_Validate_NotEmpty extends Zend_Validate_NotEmpty {
	
	/**
	 * Returns code snippet used for client-side validation.
	 * 
	 * @param string $name The name of the element.
	 * @return array The constraint and message.
	 */
	public function getClientSideValidation($name) {
		return array('mandatory', 'This field is required');
	}
}

?>