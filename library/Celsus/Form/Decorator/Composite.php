
<?php
class Celsus_Form_Decorator_Composite extends Zend_Form_Decorator_Abstract {

	public function buildLabel() {
		$element = $this->getElement();
		$name = $element->getName();
		$label = $element->getLabel();
		if ($translator = $element->getTranslator()) {
			$label = $translator->translate($label);
		}
		$label = $element->getView()->formLabel($name, $label . ':', array('escape' => false));
		if ($element->isRequired() || $element->getValidator('NotEmpty')) {
			$label = "<strong>$label</strong>";
		}
		// Quick hack to add IDs to labels - not worth subclassing for this.
		$label = str_replace(' for=', ' id="' . $name . '_label" for=', $label);
		return $label;
	}

	public function buildInput() {
		$element = $this->getElement();
		$helper  = $element->helper;
		$messages = $element->getMessages();
		if (!empty($messages)) {
			$element->setAttrib('class', $element->getAttrib('class') . ' error');
		}
		return $element->getView()->$helper(
			$element->getName(),
			$element->getValue(),
			$element->getAttribs(),
			$element->options
		);
	}

	public function buildErrors() {
		$element  = $this->getElement();
		$name = $element->getName();
		$messages = $element->getMessages();
		$message = $messages ? current($messages) : '';
		return '<span id="' . $name . '_constraint" class="form_error">' .$message . '</span>';
	}

	public function buildDescription() {
		$element = $this->getElement();
		$desc    = $element->getDescription();
		if (empty($messages)) {
			return '';
		}
		return '<div class="description">' . $desc . '</div>';
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
		$label     = $this->buildLabel();
		$input     = $this->buildInput();
		$errors    = $this->buildErrors();
		$desc      = $this->buildDescription();

		$output = '<dt>'
		. $label . '</dt><dd>'
		. $input
		. $errors
		. $desc
		. '</dd>';

		switch ($placement) {
			case (self::PREPEND):
				return $output . $separator . $content;
			case (self::APPEND):
			default:
				return $content . $separator . $output;
		}
	}
}