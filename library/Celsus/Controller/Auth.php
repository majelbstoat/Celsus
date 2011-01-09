<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

/**
 * Standard authorisation controller.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
abstract class Celsus_Controller_Auth extends Zend_Controller_Action {

	/**
	 * Logs a user out of the application.
	 */
	public function logoutAction() {
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$auth->clearIdentity();
		}
		$redirectSession = new Zend_Session_Namespace('Redirect');
		$location = $redirectSession ? $redirectSession->location : '/';
		$this->_redirect($location);
	}
}
?>