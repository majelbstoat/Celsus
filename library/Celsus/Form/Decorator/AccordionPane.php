<?php

class Celsus_Form_Decorator_AccordionPane extends Zend_Form_Decorator_Form {

	public function render($content) {
		$form = $this->getElement();
		$formName = $form->getName();
		$title = $this->getOption('title');
		$content = <<<CONTENT
		<li class="ui-accordion-group">
			<a href="#$formName" class="ui-accordion-header">$title</a>
			<div class="ui-accordion-content">$content</div>
		</li>
CONTENT;
		return $content;
	}
}