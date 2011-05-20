<?php

class Celsus_Form_Decorator_LookupReference extends Zend_Form_Decorator_Abstract {

	/**
	 * Render an item, using the lookup from the associated reference.
	 *
	 * Replaces $content entirely from currently set element.
	 *
	 * @param  string $content
	 * @return string
	 */
	public function render($content) {
		$element = $this->getElement();
		$service = $this->_options['service'];
		return $service::getDescription($element->getValue());
	}
}
