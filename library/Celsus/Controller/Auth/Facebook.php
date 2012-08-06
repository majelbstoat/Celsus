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
	public function facebookAction(Celsus_Data_Object $parameters, Celsus_Response_Model $responseModel) {

		$adapter = Celsus_Auth::getAuthAdapter();
		if (!$adapter->canAuthenticate($parameters)) {
			if ($adapter->accessDenied($parameters)) {
				$responseModel->setResponseType($responseModel::RESPONSE_TYPE_USER_DECLINED);
			} else {
				$responseModel->setResponseType($responseModel::RESPONSE_TYPE_MISSING_AUTHENTICATION);
			}

			// The request did not supply enough information to authenticate, so we bail.
			return;
		}

		$context = $parameters->context;

		$callbackUrl = Celsus_Routing::absoluteLinkTo('auth_facebook_callback', array('context' => $context));
		$adapter->setCallbackUrl($callbackUrl);
		$adapter->populateAuthorisationPayload($parameters);
		$result = $adapter->authenticate();

		if (!$result->isValid()) {
			// Error authenticating to Facebook.
			$responseModel->setResponseType($responseModel::RESPONSE_TYPE_ERROR);
			return;
		} else {
			$facebookData = $adapter->getResult();
		}

		$auth = Celsus_Auth::getInstance();

		if ($auth->hasIdentity()) {
			// Already has a session, so we really shouldn't have authenticated, but now that we have, just go to the home.
			// @todo Log this, because if it ever happens, it means the identity checking plugin isn't working.
			$this->getResponseModel()->setResponseType($responseModel::RESPONSE_TYPE_LOGGED_IN);
			return;
		} else {
			// Check the Facebook data received against the local adapter.
			$facebookId = $result->getIdentity();
			$localAdapter = $adapter->getLocalAuthAdapter();
			$localAdapter->setCredential($facebookId)->setIdentity($facebookId);
			$result = $localAdapter->authenticate();

			if ($result->isValid()) {
				// The user is successfully authenticated, so allow the application the opportunity to merge fresh data from
				// Facebook and then redirect them back to from whence they came.

				$identity = $this->_merge($localAdapter->getResult(), $facebookData);
				$auth->getStorage()->write($identity);
				$this->getResponseModel()->setResponseType($responseModel::RESPONSE_TYPE_SUCCESS);
			} else {
				$code = $result->getCode();
				if (Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS == $code) {
					// More than one result matched that provider, which is weird.
					// @todo Log this, duplicate users are a no-no.
					$this->_handleDuplicate();

				} elseif (Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND == $code) {
					// User not found, so register them locally.
					$this->_register($facebookData);
				} else {
					// Another unspecified error.
					$responseModel->setResponseType($responseModel::RESPONSE_TYPE_ERROR);
					return;
				}
			}
		}
	}

	/**
	 * Updates user information from Facebook to keep their data fresh.
	 *
	 * @param Celsus_Model $local;
	 * @param Celsus_Model $facebookData;
	 * @return Celsus_Model
	 */
	abstract protected function _merge(Celsus_Model $local, Celsus_Model $facebookData);

	/**
	 * Application-specific function that registers a Facebook user locally.
	 */
	abstract protected function _register(Celsus_Model $facebookData);

	/**
	 * Application-specific function that logs a Facebook user in locally.
	 */
	abstract public function loginAction(Celsus_Parameters $parameters, Celsus_Response_Model $responseModel);

}
