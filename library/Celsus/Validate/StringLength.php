<?php

class Celsus_Validate_StringLength extends Zend_Validate_StringLength {

	/**
	 * Returns code snippet used for client-side validation.
	 *
	 * @param string $name The name of the element.
	 * @return array The constraint and message.
	 */
	public function getClientSideValidation($name) {
		$min = $this->getMin();
		$max = $this->getMax();
		$test = "(%element%.val().length >= $min) && (%element%.val().length <= $max)";
		$message = "Please enter between $min and $max characters.";
		return array($test, $message);
	}
}

?>