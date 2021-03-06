<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Auth
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: RpxNow.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Allows authentication using Facebook OAuth.
 *
 * @category Celsus
 * @package Celsus_Auth
 */
class Celsus_Auth_Adapter_Facebook_ServerSide implements Celsus_Auth_Adapter_Interface {

	const EXCEPTION_FACEBOOK_ERROR = 'EXCEPTION_FACEBOOK_ERROR';

	/**
	 * The adapter to use to authenticate locally, once Facebook has authenticated successfully.
	 *
	 * @var Celsus_Auth_Adapter_Interface $_localAuthAdapter
	 */
	protected $_localAuthAdapter = null;

	protected $_authorisationCode = null;

	protected $_facebookAdapter = null;

	/**
	 * The callback URL used in the request to convert from a code to an access token.
	 *
	 * @var string $_callbackUrl
	 */
	protected $_callbackUrl = null;

	protected $_result = null;

	public function __construct(Celsus_Auth_Adapter_Interface $localAuthAdapter) {
		$this->setLocalAuthAdapter($localAuthAdapter);
	}

	public function setLocalAuthAdapter($localAuthAdapter) {
		$this->_localAuthAdapter = $localAuthAdapter;
	}

	/**
	 * Gets the adapter used to authenticate locally.
	 *
	 * @return Celsus_Auth_Adapter_Interface
	 */
	public function getLocalAuthAdapter() {
		return $this->_localAuthAdapter;
	}

	public function setCallbackUrl($callbackUrl) {
		$this->_callbackUrl = $callbackUrl;
	}

	public function setAuthorisationCode($authorisationCode) {
		$this->_authorisationCode = $authorisationCode;
		return $this;
	}

	public function populateAuthorisationPayload($parameters) {
		$this->setAuthorisationCode($parameters->code);
	}

	public function canAuthenticate($parameters) {
		return isset($parameters->code);
	}

	public function accessDenied($parameters) {
		return isset($parameters->access_denied);
	}

	protected function _base64UrlDecode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}

	/**
	 * Authenticates using the Facebook service.
	 *
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {

		if (!$this->_authorisationCode || !$this->_callbackUrl) {
			throw new Celsus_Exception("Missing information for Facebook authentication.", Celsus_Http::BAD_REQUEST);
		}

		$accessToken = Celsus_External_Model_Service_FacebookUser::acquireAccessToken($this->_authorisationCode, $this->_callbackUrl);

		$userData = Celsus_External_Model_Service_FacebookUser::getProfileInformation($accessToken);

		if (!count($userData)) {
			return new Celsus_Auth_Result(Celsus_Auth_Result::FAILURE, null);
		}

		$user = $userData[0];
		$user->access_token = $accessToken;
		$this->_result = $user;

		return new Celsus_Auth_Result(Celsus_Auth_Result::SUCCESS, $user->id, array('Authentication Successful'));
	}

	/**
	 * Gets the result of the authentication attempt.
	 */
	public function getResult() {
		return $this->_result;
	}
}
