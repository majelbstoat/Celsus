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

	protected $_applicationSecret = null;

	protected $_applicationId = null;

	protected $_authorisationCode = null;

	protected $_result = null;

	public function __construct($applicationId, $applicationSecret, $localAuthAdapter, $url = null) {
		$this->setApplicationId($applicationId)
			->setApplicationSecret($applicationSecret)
			->setLocalAuthAdapter($localAuthAdapter);

		if (null !== $url) {
			$this->setUrl($url);
		}
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

	public function setApplicationId($applicationId) {
		$this->_applicationId = $applicationId;
		return $this;
	}

	public function setApplicationSecret($applicationSecret) {
		$this->_applicationSecret = $applicationSecret;
		return $this;
	}

	public function setAuthorisationCode($authorisationCode) {
		$this->_authorisationCode = $authorisationCode;
		return $this;
	}

	public function populateAuthorisationPayload() {
		$this->setAuthorisationCode($_GET['code']);
	}

	public function canAuthenticate() {
		return array_key_exists('code', $_GET);
	}

	public function setUrl($url) {
		$this->_url = $url;
		return $this;
	}

	protected function _base64UrlDecode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}

	/**
	 * Authenticates via cURL.
	 *
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {

		if (!$this->_applicationId || !$this->_applicationSecret || !$this->_authorisationCode) {
			throw new Zend_Auth_Adapter_Exception("Missing information for Facebook authentication.");
		}

		// By convention, the callback path will be set in the config.
		// @todo This will have to be updated, as it only allows for FB connection in one context.
		$callbackPath = Zend_Registry::get('config')->auth->facebook->callbackPath;

		$parameters = array(
			'client_id' => $this->_applicationId,
			'client_secret' => $this->_applicationSecret,
			'redirect_uri' => Celsus_Application::rootUrl() . $callbackPath,
			'grant_type' => 'authorization_code',
			'code' => $this->_authorisationCode
		);

		$accessTokenResponse = file_get_contents("https://graph.facebook.com/oauth/access_token?" . http_build_query($parameters));
		$responseParameters = null;
		parse_str($accessTokenResponse, $responseParameters);

		$user = json_decode(file_get_contents("https://graph.facebook.com/me?access_token=" . $responseParameters['access_token']));

		$user->access_token = $responseParameters['access_token'];
		$this->_result = $user;

		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user->id, array('Authentication Successful'));
	}

	/**
	 * Gets the result of the authentication attempt.
	 */
	public function getResult() {
		return $this->_result;
	}
}
?>