<?php

class Celsus_Form_Decorator_ClearedForm extends Zend_Form_Decorator_Form {
	
	public function render($content) {
		return parent::render($content . '<div class="clear"></div>');
	}
}
?>