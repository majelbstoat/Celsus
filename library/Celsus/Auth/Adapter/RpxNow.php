<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Auth
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

/**
 * Allows authentication against JanRain's Rpx platform (Engage).
 *
 * @category Celsus
 * @package Celsus_Auth
 */
class Celsus_Auth_Adapter_RpxNow implements Zend_Auth_Adapter_Interface {

	const DEFAULT_RPX_URL = 'https://rpxnow.com/api/v2/auth_info';

	const EXCEPTION_RPX_ERROR = 'EXCEPTION_RPX_ERROR';

	protected $_token = null;

	protected $_apiKey = null;

	protected $_result = null;

	protected $_url = self::DEFAULT_RPX_URL;

	public function __construct($token, $apiKey = null, $url = null) {
		$this->_token = $token;

		if (null !== $apiKey) {
			$this->setApiKey($apiKey);
		}

		if (null !== $url) {
			$this->setUrl($url);
		}
	}

	public function setApiKey($apiKey) {
		$this->_apiKey = $apiKey;
		return $this;
	}

	public function setUrl($url) {
		$this->_url = $url;
		return $this;
	}

	/**
	 * Authenticates via cURL.
	 *
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {

		if (!$this->_apiKey || !$this->_token || !$this->_url) {
			throw new Zend_Auth_Adapter_Exception("Missing information for Rpx authentication.");
		}

		$postData = array(
			'token' => $this->_token,
			'apiKey' => $this->_apiKey,
			'format' => 'json',
			'extended' => 'true'
		);

		// Send the appropriate data to RPX via cURL for authentication.
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $this->_url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$rawJson = curl_exec($curl);
		curl_close($curl);

		$authInfo = json_decode($rawJson, true);

		if ('ok' == $authInfo['stat']) {
			$this->_result = $authInfo['profile'];
			$resultInfo = array(
				'code' => Zend_Auth_Result::SUCCESS,
				'identity' => $this->_token,
				'messages' => array(
					"Authentication Successful"
				)
			);
		} else {
			$resultInfo = array(
				'code' => Zend_Auth_Result::FAILURE,
				'identity' => $this->_token,
				'messages' => array(
					"Authentication via RPX Failed"
				)
			);
		}
		return new Zend_Auth_Result($resultInfo['code'], $resultInfo['identity'], $resultInfo['messages']);
	}

	/**
	 * Gets the result of the authentication attempt.
	 */
	public function getResult() {
		return $this->_result;
	}
}
?>