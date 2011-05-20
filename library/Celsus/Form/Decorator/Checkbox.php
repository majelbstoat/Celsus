<?php
class Celsus_Form_Decorator_Checkbox extends Zend_Form_Decorator_Abstract {

	public function buildInput() {
		$element = $this->getElement();
		$helper  = $element->helper;
		return $element->getView()->$helper(
			$element->getName(),
			$element->getValue(),
			$element->getAttribs(),
			$element->options
		);
	}

	public function render($content) {
		$element = $this->getElement();
		if (!$element instanceof Zend_Form_Element) {
			return $content;
		}
		if (null === $element->getView()) {
			return $content;
		}
		$name = $element->getName();
		$label = $element->getLabel();
		$separator = $this->getSeparator();


		$output = '<dt id="' . $name . '-label"></dt><dd><label for="' . $name . '">' . $this->buildInput() . $label . '</label></dd>';
		return $content . $separator . $output;
	}
}