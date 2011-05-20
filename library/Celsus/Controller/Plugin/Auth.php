<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Auth.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Handles authentication checking.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {

	protected $_ignoredControllers = array('auth', 'error');

	/**
 	 * Checks for an identity if necessary, and redirects to the login page if there isn't one.
 	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		// If we're on a non-existant route, or we're in the auth controller, don't bother to check.
		$frontController = Zend_Controller_Front::getInstance();
		if (!$frontController->getDispatcher()->isDispatchable($request) || in_array($request->getControllerName(), $this->_ignoredControllers) || $request->getParam('error_handler')) {
			return;
		}

		$auth = Celsus_Auth::getInstance();

		if (!$auth->hasIdentity()) {
			$request->setControllerName('auth')->setActionName('login');
		}
	}
}
?>
