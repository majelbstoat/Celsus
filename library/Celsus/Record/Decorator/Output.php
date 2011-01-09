
<?php
class Celsus_Record_Decorator_Output extends Zend_Form_Decorator_Abstract {

	public function buildLabel() {
		$element = $this->getElement();
		$name = $element->getName();
		$label = $element->getLabel();
		if ($translator = $element->getTranslator()) {
			$label = $translator->translate($label);
		}
		$label = "<strong>" . $element->getView()->formLabel($name, $label . ':', array('escape' => false)) . "</strong>";		
		return $label;
	}

	public function render($content) {
		$element = $this->getElement();
		if (!$element instanceof Zend_Form_Element) {
			return $content;
		}
		if (null === $element->getView()) {
			return $content;
		}

		$separator = $this->getSeparator();
		$placement = $this->getPlacement();
		$label = $this->buildLabel();
		
		$output = '<dt>'
		. $label . '</dt><dd>'
		. $content
		. '</dd>';

		return $output;
	}
}