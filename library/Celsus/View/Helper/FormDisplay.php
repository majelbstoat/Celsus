<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: FormDisplay.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * View Helper that renders Display elements.
 *
 * @class Celsus_View_Helper_FormDisplay
 * @ingroup Celsus_View_Helpers
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