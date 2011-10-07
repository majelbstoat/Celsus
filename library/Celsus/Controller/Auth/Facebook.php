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
 * Default auth controller that authenticates via Facebook.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
abstract class Celsus_Controller_Auth_Facebook extends Celsus_Controller_Auth {

	/**
	 * Handles an inbound RPX-based auth request.
	 */
	public function facebookAction() {

		if ($this->getRequest()->isPost()) {

			$adapter = Celsus_Auth::getAuthAdapter();
			$adapter->setSignedRequest($_POST['signed_request']);
			$result = $adapter->authenticate();

			if (!$result->isValid()) {
				// This shouldn't be possible, but just in case, bail gracefully.
				$this->getRequest()->setParam('error_handler', Celsus_Auth_Adapter_Facebook::EXCEPTION_FACEBOOK_ERROR);
				$this->_forward('error', 'error');
			} else {
				$facebookData = $adapter->getResult();
				Zend_Registry::set('facebookData', $facebookData);
			}

			$auth = Celsus_Auth::getInstance();
			if ($auth->hasIdentity()) {
				// Already has a session, so we really shouldn't have authenticated, but now that we have, just go to the home.
				$this->_redirect('/');
			} else {
				// Check the facebook data received against the local adapter.

				$localAdapter = $adapter->getLocalAuthAdapter();
				$facebookId = $result->getIdentity();
				$localAdapter->setCredential($facebookId)->setIdentity($facebookId);
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
						// User not found, so save their details locally.
						$this->_register();
					} else {
						// Another unspecified error.
						$this->getRequest()->setParam('error_handler', Celsus_Auth_Adapter_Couch::EXCEPTION_COUCH_AUTH_ERROR);
						$this->_forward('error', 'error');
					}
				}
			}
		} else {
			// Trying to access this URL by means other than POST is disallowed.
			$this->_redirect('/');
		}
	}

	/**
	 * Application-specific function that registers a Facebook user locally.
	 */
	abstract protected function _register();

	abstract public function loginAction();
}
