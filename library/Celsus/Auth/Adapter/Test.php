<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Auth
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Test.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Dummy authentication adapter for testing.
 *
 * @category Celsus
 * @package Celsus_Auth
 */
class Celsus_Auth_Adapter_Test implements Celsus_Auth_Adapter_Interface {

	/**
	 * Mock identities that we can authenticate against.
	 *
	 * @var array $_identities
	 */
	protected $_identities = null;

	protected $_identity = null;

	protected $_credential = null;

	protected $_result = null;

	public function __construct(array $identities) {
		$this->_identities = $identities;
	}

	public function setIdentity($identity) {
		$this->_identity = $identity;
		return $this;
	}

	public function setCredential($credential) {
		$this->_credential = $credential;
		return $this;
	}

	public function authenticate() {

		$resultInfo['identity'] = null;
		$resultInfo['messages'] = array();
		if (isset($this->_identities[$this->_identity])) {
			if ($this->_credential == $this->_identities[$this->_identity]) {
				$resultInfo['code'] = Zend_Auth_Result::SUCCESS;
				$resultInfo['identity'] = $this->_identity;
			} else {
				$resultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
				$resultInfo['messages'] = array("Invalid credential");
			}
		} else {
			$resultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
			$resultInfo['messages'] = array("Invalid username");
		}

		$this->_result = new Zend_Auth_Result($resultInfo['code'], $resultInfo['identity'], $resultInfo['messages']);
		return $this->_result;
	}

	public function getResult() {
		// Mock object doesn't need to worry about hiding passwords etc.
		return $this->_result;
	}
}