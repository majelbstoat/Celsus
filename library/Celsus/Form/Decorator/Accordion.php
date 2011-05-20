<?php

class Celsus_Form_Decorator_Accordion extends Zend_Form_Decorator_Form {

	public function render($content) {
		$form = $this->getElement();
		$formName = $form->getName();

		$content = <<<CONTENT
		<ul class="accordion">$content</ul>
CONTENT;

		// Add the on-load code and request UI inclusion.
		$jQuery = $form->getView()->getHelper('jQuery');
		$jQuery->addOnLoad("$('.accordion').accordion({autoHeight: false})")->uiEnable(true);
		return $content;


	}

}