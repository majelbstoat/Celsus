<?php

/**
 * Simply renders the element's value as its content.
 */
class Celsus_Form_Decorator_RawValue extends Zend_Form_Decorator_Form {
	
	public function render($content) {
		return $this->getElement()->getValue();
	}
}