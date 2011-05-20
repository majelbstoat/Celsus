<?php

class Celsus_Auth_Adapter_UserService implements Celsus_Auth_Adapter_Interface {

	protected $_credential = null;

	protected $_identity = null;

	protected $_result = null;

	protected $_userClass = null;

	public function __construct($userClass) {
		if (!in_array('Celsus_Model_Service_User_Interface', class_implements($userClass, true))) {
			throw new Zend_Auth_Adapter_Exception("$userClass must implement Celsus_Model_Service_User_Interface");
		}
		$this->_userClass = $userClass;
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
		$userClass = $this->_userClass;

		$results = $userClass::findByIdentityAndCredential($this->_identity, $this->_credential);

		$resultInfo['code'] = Zend_Auth_Result::SUCCESS;
		$resultInfo['identity'] = $this->_identity;
		$resultInfo['messages'] = array('Authentication successful');

		$resultCount = count($results);
		if ($resultCount < 1) {
			$resultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
			$resultInfo['messages'] = array('A user with the supplied identity could not be found.');
		} elseif ($resultCount > 1) {
			$resultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
			$resultInfo['messages'] = array('More than one user matches the supplied identity.');
		} else {
			$result = $results[0];

			// Additional checks to make sure the view is returning the field we want.
			$expectedField = $userClass::getIdentityField();
			$identity = $result->$expectedField;
			if (is_string($identity) && ($identity !== $this->_identity)) {
				$resultInfo['code'] = Zend_Auth_Result::FAILURE;
				$resultInfo['messages'] = array('The returned document did not include the identity in the expected field.');
			} elseif (is_array($identity)) {
				$flipped = array_flip($identity);
				if (!isset($flipped[$this->_identity])) {
					$resultInfo['code'] = Zend_Auth_Result::FAILURE;
					$resultInfo['messages'] = array('The returned document did not include the identity in the expected field.');
				}
			}
		}

		if (Zend_Auth_Result::SUCCESS == $resultInfo['code']) {
			// We passed all the tests, so set the result object.
			$this->_result = $result;
		}

		return new Zend_Auth_Result($resultInfo['code'], $resultInfo['identity'], $resultInfo['messages']);
	}

	public function getResult() {
		return $this->_result;
	}

}