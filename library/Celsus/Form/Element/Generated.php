<?php
class Celsus_Form_Element_Generated extends Zend_Form_Element {
	
	/**
	 * Sets the ignore flag on generated attributes.
	 *
	 * @param unknown_type $config
	 */
	public function __construct($config = null) {
		parent::__construct($config);
		$this->setIgnore(true);
	}
	
	/**
	 * As this is a placeholder element, render nothing on the client side.
	 *
	 * @return string
	 */
	public function render() {
		return '';
	}
}
