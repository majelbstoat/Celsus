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

	const REGISTRY_KEY_FACEBOOK_DATA = 'facebookData';

	const ENDPOINT_OAUTH = "https://www.facebook.com/dialog/oauth";

	/**
	 * Handles an inbound Facebook-based auth request.
	 */
	public function facebookAction() {

		$adapter = Celsus_Auth::getAuthAdapter();
		if (!$adapter->canAuthenticate()) {
			if ($adapter->accessDenied()) {
				$this->_userDeclined();
			} else {
				$this->_missingAuthentication();
			}

			// The request did not supply enough information to authenticate, so we bail.
			// @todo We probably don't always want to go to home.
			return $this->_redirect(Celsus_Routing::linkTo('home'));
		}

		$context = $this->getRequest()->getParam('context');
		$callbackUrl = Celsus_Routing::absoluteLinkTo('auth_facebook_callback', array('context' => $context));
		$adapter->setCallbackUrl($callbackUrl);
		$adapter->populateAuthorisationPayload();
		$result = $adapter->authenticate();

		if (!$result->isValid()) {
			// This shouldn't be possible, but just in case, bail gracefully.  Might be possible due to timeouts for example.
			$this->getRequest()->setParam(Celsus_Error::ERROR_FLAG, Celsus_Auth::EXCEPTION_AUTH_ERROR);
			$this->_forward('error', 'error');
		} else {
			$facebookData = $adapter->getResult();
			Zend_Registry::set(self::REGISTRY_KEY_FACEBOOK_DATA, $facebookData);
		}

		$auth = Celsus_Auth::getInstance();

		if ($auth->hasIdentity()) {
			// Already has a session, so we really shouldn't have authenticated, but now that we have, just go to the home.
			// @todo Log this, because if it ever happens, it means the identity checking plugin isn't working.
			return $this->_redirect(Celsus_Routing::linkTo('home'));
		} else {
			// Check the Facebook data received against the local adapter.
			$localAdapter = $adapter->getLocalAuthAdapter();
			$facebookId = $result->getIdentity();
			$localAdapter->setCredential($facebookId)->setIdentity($facebookId);
			$result = $localAdapter->authenticate();

			if ($result->isValid()) {
				// The user is successfully authenticated, so allow the application the opportunity to merge fresh data from
				// Facebook and then redirect them back to from whence they came.

				$identity = $this->_merge($localAdapter->getResult());
				$auth->getStorage()->write($identity);
				$redirectSession = new Zend_Session_Namespace('Redirect');
				$location = $redirectSession->location ? $redirectSession->location : Celsus_Routing::linkTo('home');
				return $this->_redirect($location);
			} else {
				$code = $result->getCode();
				if (Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS == $code) {
					// More than one result matched that provider, which is weird.
					// @todo Log this, duplicate users are a no-no.
					$this->_handleDuplicate();

				} elseif (Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND == $code) {
					// User not found, so save their details locally.
					$this->_register();
				} else {
					// Another unspecified error.
					$this->getRequest()->setParam(Celsus_Error::ERROR_FLAG, Celsus_Auth::EXCEPTION_AUTH_ERROR);
					$this->_forward('error', 'error');
				}
			}
		}
	}

	/**
	 * Updates user information from Facebook to keep their data fresh.
	 *
	 * @param Celsus_Model $local;
	 * @return Celsus_Model
	 */
	protected function _merge($local) {
		return $local;
	}

	/**
	 * Application-specific function that handles missing authentication data.
	 */
	abstract protected function _missingAuthentication();

	/**
	 * Application-specific function that handles the user rejecting facebook permissions.
	 */
	abstract protected function _userDeclined();

	/**
	 * Application-specific function that registers a Facebook user locally.
	 */
	abstract protected function _register();

	/**
	 * Application-specific function that logs a Facebook user in locally.
	 */
	abstract public function loginAction();

}
