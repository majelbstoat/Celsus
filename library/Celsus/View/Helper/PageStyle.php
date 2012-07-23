<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: DisplayFeedback.php 72 2010-09-14 01:56:33Z jamie $
 */

/**
 * View helper to set a page's styles in the head.
 *
 * @class Celsus_View_Helper_PageStyle
 * @ingroup Celsus_View_Helpers
 */
class Celsus_View_Helper_PageStyle extends Celsus_View_Helper_Tag {

	protected $_styles = array();

	/**
	 * Appends the supplied styles to the end of the style list.
	 *
	 * @param string|array $components
	 */
	public function append($styles) {
		if (!is_array($styles)) {
			$styles = array($styles);
		}
		$this->_styles = $this->_styles + $styles;
	}

	/**
	 * Prepends the supplied styles to the beginning of the style list.
	 *
	 * @param string|array $components
	 */
	public function prepend($styles) {
		if (!is_array($styles)) {
			$styles = array($styles);
		}
		$this->_styles = $styles + $this->_styles;
	}

	/**
	 * Displays a title for the page.
	 *
	 *
	 */
	public function render() {
		foreach ($this->_styles as $style) {
			// @todo Find the bundles that are required for the style.
			$this->_addTag('link', array(
				'rel' => 'stylesheet',
				'type' => 'text/css',
				'href' => "/css/$style.css"
			));
		}
		$this->_tags();
	}
}
