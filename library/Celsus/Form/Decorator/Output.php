<?php
class Celsus_Form_Decorator_Output extends Zend_Form_Decorator_Abstract {

	public function buildLabel() {
		$element = $this->getElement();
		$name = $element->getName();
		$label = $element->getLabel();
		if ($translator = $element->getTranslator()) {
			$label = $translator->translate($label);
		}
		$label = "<strong>" . $element->getView()->formLabel($name, $label, array('escape' => false)) . "</strong>";
		return $label;
	}

	public function render($content) {
		if (!$content) {
			// Don't output the row if there's no value.
			return $content;
		}

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
		. '<span>' . $content
		. '</span></dd>';

		return $output;
	}
}