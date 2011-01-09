<?php
class Celsus_Form_Element_Display extends Zend_Form_Element {

	/**
	 * Render using the formDisplay helper.
	 * 
	 * @var string
	 */
	public $helper = 'formDisplay';
	
	/**
	 * Sets the ignore flag on display attributes.
	 *
	 * @param unknown_type $config
	 */
	public function __construct($config = null) {
		parent::__construct($config);
		$this->setIgnore(true);
	}

	/**
	 * Load default decorators for Display attributes.
	 *
	 * @return void
	 */
	public function loadDefaultDecorators() {
		if ($this->loadDefaultDecoratorsIsDisabled()) {
			return;
		}
		
		$this->addPrefixPath('Celsus_Form_Decorator', 'Celsus/Form/Decorator', 'decorator');

		$decorators = $this->getDecorators();
		if (empty($decorators)) {
			$this->addDecorator('RawValue');
		}
	}

	public function setText($text) {
		$this->setAttrib('text', $text);
	}
}
