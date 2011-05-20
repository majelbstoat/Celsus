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
		$editUrl = $record->getEditUrl();
		$attribs = array();
		foreach ($record->getAttribs() as $attrib => $value) {
			$attribs[] = $attrib . '="' . $value . '"';
		}
		$attributes = $attribs ? ' ' . implode(' ', $attribs) : '';

		// Add a head link to the modify page.
		$record->getView()->headLink()->headLink(array(
			'rel' => 'edit',
			'href' => $editUrl
		));

		$jQuery = Zend_Layout::getMvcInstance()->getView()->getHelper('jQuery');
		$jQuery->addPlugin('record');


		return <<<RECORD
<div$attributes>
	$content
</div>
RECORD;
	}
}
