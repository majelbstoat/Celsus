<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Partial.php 49 2010-07-18 23:23:29Z jamie $
 */

/**
 * Controller intended to be used for AJAX and partial content requests.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
abstract class Celsus_Controller_Partial extends Zend_Controller_Action {

	/**
	 * Prevents actions in this class being accessed directly in the browser.
	 */
	public function init() {
		$frontRequest = Zend_Controller_Front::getInstance()->getRequest();
		$thisRequest = $this->getRequest();
		if ($frontRequest->getControllerName() == $thisRequest->getControllerName() && $frontRequest->getModuleName() == $thisRequest->getModuleName()) {
			$this->_redirect('/');
		}
		parent::init();
	}
}
?>