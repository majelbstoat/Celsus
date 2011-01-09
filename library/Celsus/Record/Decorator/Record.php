<?php

class Celsus_Record_Decorator_Record extends Zend_Form_Decorator_Abstract {

	/**
	 * Render a form
	 *
	 * Replaces $content entirely from currently set element.
	 *
	 * @param  string $content
	 * @return string
	 */
	public function render($content)
	{
		$record = $this->getElement();
		$attribs = array();
		foreach ($record->getAttribs() as $attrib => $value) {
			$attribs[] = $attrib . '="' . $value . '"';
		}
		$attributes = $attribs ? ' ' . implode(' ', $attribs) : '';
		
		return "<div$attributes>" . $content . '</div>';
	}
}
