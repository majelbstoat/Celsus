<?php

/**
 * View Helper that renders Display elements.
 * 
 * @author jamest
 */
class Celsus_View_Helper_FormDisplay extends Zend_View_Helper_FormElement {
	
	public function formDisplay($name, $value = null, $attribs = null) {
		
		// Make sure the hidden element doesn't include a helper attribute.		
		unset($attribs['helper']);

		$text = isset($attribs['text']) ? $attribs['text'] : $value;
		unset($attribs['text']);
		
		// We still put a hidden input in place, for consistency when validating the form.
		$xhtml = $this->_hidden($name, $value, $attribs);

		return $text . $xhtml;
	}
	
}