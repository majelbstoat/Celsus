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

			$adapter = Celsus_Auth::getAuthAdapter();
			$adapter->setToken($_POST['token']);
			$result = $adapter->authenticate();

			if (!$result->isValid()) {
				// Something went wrong with RPX.  Bail Gracefully.
				$this->getRequest()->setParam(Celsus_Error::ERROR_FLAG, Celsus_Auth_Adapter_RpxNow::EXCEPTION_RPX_ERROR);
				$this->_forward('error', 'error');
			} else {
				$rpxData = $adapter->getResult();
				Zend_Registry::set('rpxData', $rpxData);
			}

			$auth = Celsus_Auth::getInstance();
			if ($auth->hasIdentity()) {
				// Already has a session, so offer the choice of adding this identity.
				$this->_forward('merge');
			} else {
				// Check the RPX data received against the local adapter.

				$localAdapter = $adapter->getLocalAuthAdapter();
				$localAdapter->setCredential($rpxData['identifier'])->setIdentity($rpxData['identifier']);
				$result = $localAdapter->authenticate();

				if ($result->isValid()) {
					// The user is successfully authenticated, so redirect them back to from whence they came.
					$auth->getStorage()->write($adapter->getResult());
					$redirectSession = new Zend_Session_Namespace('Redirect');
					$location = $redirectSession->location ? $redirectSession->location : '/';
					$this->_redirect($location);
				} else {
					if (Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS == $result->getCode()) {
						// More than one result matched that provider, which is weird.

					} elseif (Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND == $result->getCode()) {
						// User not found, so register them.
						$this->_forward('register');
					} else {
						// Another unspecified error.
						$this->getRequest()->setParam(Celsus_Error::ERROR_FLAG, Celsus_Auth_Adapter_Couch::EXCEPTION_COUCH_AUTH_ERROR);
						$this->_forward('error', 'error');
					}
				}
			}
		} else {
			// Trying to access this URL by means other than POST is disallowed.
			$this->_redirect('/');
		}
	}

	abstract public function registerAction();

	abstract public function loginAction();

	/**
	 * Adds an identity to an already logged in profile.
	 */
	abstract public function mergeAction();
}
