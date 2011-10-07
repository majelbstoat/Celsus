<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Auth
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Couch.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Allows authentication from a CouchDb database.
 *
 * @category Celsus
 * @package Celsus_Auth
 */
class Celsus_Auth_Adapter_Couch implements Celsus_Auth_Adapter_Interface {

	const EXCEPTION_COUCH_AUTH_ERROR = 'EXCEPTION_COUCH_AUTH_ERROR';

	/**
	 * The adapter to authenticate with.
	 *
	 * @var Celsus_Db_Document_Adapter_Couch
	 */
	protected $_adapter = null;

	protected $_adapterName = null;

	protected $_designDocument = null;

	protected $_view = null;

	protected $_identity = null;

	protected $_identityField = 'id';

	protected $_credential = null;

	protected $_result;

	public function __construct($adapterName, $identityField = null, $designDocument = null, $view = null) {
		$this->_adapterName = $adapterName;

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

	/**
	 * Gets the database adapter used to authenticate.
	 *
	 * @throws Celsus_Exception
	 * @return Celsus_Db_Document_Adapter_Couch
	 */
	public function getAdapter() {
		if (null == $this->_adapter) {
			if (null === $this->_adapterName) {
				throw new Celsus_Exception("Adapter name not specified!");
			}
			$this->_adapter = Celsus_Db::getAdapter($this->_adapterName);
		}
		return $this->_adapter;
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

		if (!$this->_credential || !$this->_identityField) {
			throw new Zend_Auth_Adapter_Exception("Missing information for Couch authentication.");
		}

		if ($this->_view) {
			if (!$this->_designDocument) {
				throw new Zend_Auth_Adapter_Exception("Must supply a design document when authenticating with views.");
			}

			// We are authenticating using a view.
			$parameters = array(
				"key" => $this->_credential,
				"include_docs" => true
			);
			$view = new Celsus_Db_Document_View(array(
				'name' => $this->_view,
				'designDocument' => $this->_designDocument,
				'parameters' => $parameters
			));
			$results = $this->getAdapter()->view($view);
		} else {
			// We are authenticating based on the id.
			$results = $this->getAdapter()->find($this->_credential);
		}

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
			$result = $results->current();

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