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
 * Allows authentication from a CouchDb database.
 *
 * @category Celsus
 * @package Celsus_Auth
 */
class Celsus_Auth_Adapter_Couch implements Zend_Auth_Adapter_Interface {

	const EXCEPTION_COUCH_AUTH_ERROR = 'EXCEPTION_COUCH_AUTH_ERROR';

	/**
	 * The adapter to authenticate with.
	 *
	 * @var Celsus_Db_Document_Adapter_Couch
	 */
	protected $_adapter = null;

	protected $_designDocument = null;

	protected $_view = null;

	protected $_identity = null;

	protected $_identityField = null;

	protected $_credential = null;

	protected $_result;

	public function __construct(Celsus_Db_Document_Adapter_Couch $adapter, $designDocument = null, $view = null, $identityField = null) {
		$this->_adapter = $adapter;

		if (null !== $view) {
			$this->setView($view);
		}

		if (null !== $identityField) {
			$this->setIdentityField($identityField);
		}

		if (null !== $designDocument) {
			$this->setDesignDocument($designDocument);
		}
	}

	public function setDesignDocument($designDocument) {
		$this->_designDocument = $designDocument;
		return $this;
	}

	public function setView($view) {
		$this->_view = $view;
		return $this;
	}

	public function setIdentity($identity) {
		$this->_identity = $identity;
		return $this;
	}

	public function setIdentityField($identityField) {
		$this->_identityField = $identityField;
		return $this;
	}

	public function setCredential($credential) {
		$this->_credential = $credential;
		return $this;
	}

	public function getResult() {
		return $this->_result;
	}

	/**
	 * Authenticates.
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {

		if (!$this->_credential || !$this->_view || !$this->_designDocument) {
			throw new Zend_Auth_Adapter_Exception("Missing information for Couch authentication.");
		}

		$parameters = array(
			"key" => $this->_credential,
			"include_docs" => true
		);
		$view = new Celsus_Db_Document_View(array(
			'name' => $this->_view,
			'designDocument' => $this->_designDocument,
			'parameters' => $parameters
		));
		$results = $this->_adapter->view($view);

		// Assume it was all good.
		$resultInfo['code'] = Zend_Auth_Result::SUCCESS;
		$resultInfo['identity'] = $this->_identity;
		$resultInfo['messages'] = array('Authentication successful');

		$resultCount = count($results);
		if ($resultCount < 1) {
			$resultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
			$resultInfo['messages'] = array('A document with the supplied identity could not be found.');
		} elseif ($resultCount > 1) {
			$resultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
			$resultInfo['messages'] = array('More than one document matches the supplied identity.');
		} else {
			$result = new Celsus_Data_Object($results->rewind());

			// Additional checks to make sure the view is returning the field we want.
			$identityField = $result->{$this->_identityField};
			if (is_string($identityField) && ($identityField !== $this->_identity)) {
				$resultInfo['code'] = Zend_Auth_Result::FAILURE;
				$resultInfo['messages'] = array('The returned document did not include the identity in the expected field.');
			} elseif (is_array($identityField)) {
				$flipped = array_flip($identityField);
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
}