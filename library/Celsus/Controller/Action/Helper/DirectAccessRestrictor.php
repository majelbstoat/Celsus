<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: TaskExecutor.php 49 2010-07-18 23:23:29Z jamie $
 */

/**
 * Ensures the controller can't be accessed directly in the browser.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Action_Helper_DirectAccessRestrictor extends Zend_Controller_Action_Helper_Abstract {

	public function check() {
		$frontRequest = $this->getFrontController()->getRequest();
		$thisRequest = $this->getActionController()->getRequest();
		if ($frontRequest->getControllerName() == $thisRequest->getControllerName() && $frontRequest->getModuleName() == $thisRequest->getModuleName()) {
			return $this->getActionController()->getHelper('redirector')->gotoUrl("/");
		}
	}
}