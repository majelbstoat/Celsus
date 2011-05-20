<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: RpxNow.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Default auth controller that authenticates via Rpx.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
abstract class Celsus_Controller_Auth_RpxNow extends Celsus_Controller_Auth {

	/**
	 * Handles an inbound RPX-based auth request.
	 */
	public function rpxAction() {

		if ($this->getRequest()->isPost()) {

			$adapter = new Celsus_Auth_Adapter_RpxNow($_POST['token'], Zend_Registry::get('config')->auth->rpx->key);
			$result = $adapter->authenticate();

			if (!$result->isValid()) {
				// Something went wrong with RPX.  Bail Gracefully.
				$this->getRequest()->setParam('error_handler', Celsus_Auth_Adapter_RpxNow::EXCEPTION_RPX_ERROR);
				$this->_forward('error', 'error');
			} else {
				Zend_Registry::set('rpxData', $adapter->getResult());
			}

			$auth = Zend_Auth::getInstance();
			if ($auth->hasIdentity()) {
				// Already has a session, so offer the choice of adding this identity.
				$this->_forward('merge');
			} else {
				// Application specific handling of new identities.
				$this->_loginOrRegister();
			}
		} else {
			// Trying to access this URL by means other than POST is disallowed.
			$this->_redirect('/');
		}
	}

	abstract protected function _loginOrRegister();

	abstract public function registerAction();

	abstract public function loginAction();

	/**
	 * Adds an identity to an already logged in profile.
	 */
	abstract public function mergeAction();
}
