<?php
class Celsus_Form_Element_NullSelect extends Zend_Form_Element_Select {

	/**
	 * The title to be used for the Null value option.
	 * @var string
	 */
	protected $_defaultTitle = 'Not Set';
	
	public function setMultiOptions($array) {
		$array = array('' => $this->_defaultTitle) + $array;
		return parent::setMultiOptions($array);
	}
	
	/**
	 * Sets the select box's default title.
	 * 
	 * @param string $title
	 */
	public function setDefaultTitle($title) {
		$this->_defaultTitle = $title;
	}
	
	/**
	 * Returns a null value if the default option is selected, otherwise the standard value.
	 * 
	 * @return mixed
	 */
	public function getValue() {
		if ('' === $this->_value) {
			return null;
		}
		return parent::getValue();
	}
}
?>