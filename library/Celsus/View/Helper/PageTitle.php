<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: DisplayFeedback.php 72 2010-09-14 01:56:33Z jamie $
 */

/**
 * View helper to set a page's title in the head.
 *
 * @class Celsus_View_Helper_PageTitle
 * @ingroup Celsus_View_Helpers
 */
class Celsus_View_Helper_PageTitle extends Celsus_View_Helper {

	protected $_components = array();

	/**
	  * The separator to put between components in the title.
	  *
	  * @var string
	  */
	protected $_separator = ":";

	/**
	  * @param string $separator
	  * @return Celsus_View_Helper_PageTitle
	  */
	public function setSeparator($separator) {
		$this->_separator = $separator;
		return $this;
	}

	/**
	 * Appends the supplied components to the end of the page title.
	 *
	 * @param string|array $components
	 */
	public function append($components) {
		if (!is_array($components)) {
			$components = array($components);
		}
		$this->_components = $this->_components + $components;
	}

	/**
	 * Prepends the supplied components to the beginning of the page title.
	 *
	 * @param string|array $components
	 */
	public function prepend($components) {
		if (!is_array($components)) {
			$components = array($components);
		}
		$this->_components = $components + $this->_components;
	}

	/**
	 * Displays a title for the page.
	 */
	public function render() {
		?><title><?= implode(array_filter($this->_components), " $this->_separator "); ?></title><?php
	}
}
