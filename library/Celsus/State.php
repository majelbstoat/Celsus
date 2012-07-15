<?php

class Celsus_State {

	/**
	  * The config object
	  *
	  * @var Zend_Config
	  */
	protected $_config = null;

	/**
	  * The context for this request
	  *
	  * @var string
	  */
	protected $_context = null;

	/**
	  * The exception object
	  *
	  * @var Celsus_Exception
	  */
	protected $_exception = null;

	/**
	  * The set of parameters supplied by the client
	  *
	  * @var array
	  */
	protected $_parameters = null;

	/**
	  * The request object
	  *
	  * @var Zend_Controller_Request_Abstract
	  */
	protected $_request = null;

	/**
	  * The response object
	  *
	  * @var Zend_Controller_Response_Abstract
	  */
	protected $_response = null;

	/**
	  * The response model object
	  *
	  * @var Celsus_Response_Model
	  */
	protected $_responseModel = null;

	/**
	  * The route object
	  *
	  * @var Celsus_Route
	  */
	protected $_route = null;

	/**
	  * The view model
	  *
	  * @var Celsus_View_Model
	  */
	protected $_viewModel = null;

	/**
	  * @return Celsus_Response_Model
	  */
	public function getResponseModel() {
		return $this->_responseModel;
	}

	/**
	  * @param Celsus_Response_Model
	  * @return Celsus_State
	  */
	public function setResponseModel(Celsus_Response_Model $responseModel) {
		$this->_responseModel = $responseModel;
		return $this;
	}

	/**
	  * @return string
	  */
	public function getContext() {
		return $this->_context;
	}

	/**
	  * @param string
	  * @return Celsus_State
	  */
	public function setContext($context) {
		$this->_context = $context;
		return $this;
	}


	/**
	  * @return Zend_Config
	  */
	public function getConfig() {
		return $this->_config;
	}

	/**
	  * @param Zend_Config
	  * @return Celsus_State
	  */
	public function setConfig(Zend_Config $config) {
		$this->_config = $config;
		return $this;
	}

	/**
	  * @return array
	  */
	public function getParameters() {
		return $this->_parameters;
	}

	/**
	  * @param array
	  * @return Celsus_State
	  */
	public function setParameters(array $parameters) {
		$this->_parameters = $parameters;
		return $this;
	}


	/**
	  * @return Celsus_Exception
	  */
	public function getException() {
		return $this->_exception;
	}

	/**
	 * @return boolean
	 */
	public function hasException() {
		return !!$this->_exception;
	}

	/**
	  * @param Celsus_Exception
	  * @return Celsus_State
	  */
	public function setException($exception) {
		$this->_exception = $exception;
		return $this;
	}

	/**
	  * @return Celsus_Route
	  */
	public function getRoute() {
		return $this->_route;
	}

	/**
	  * @param Celsus_Route
	  * @return Celsus_State
	  */
	public function setRoute($route) {
		$this->_route = $route;
		return $this;
	}

	/**
	  * @return Celsus_View_Model
	  */
	public function getViewModel() {
		return $this->_viewModel;
	}

	/**
	  * @param Celsus_View_Model
	  * @return Celsus_State
	  */
	public function setViewModel($viewModel) {
		$this->_viewModel = $viewModel;
		return $this;
	}

	/**
	  * @return Zend_Controller_Response_Abstract
	  */
	public function getResponse() {
		if (null === $this->_response) {
			$this->_response = new Celsus_Controller_Response_Http();
		}
		return $this->_response;
	}

	/**
	  * @param Zend_Controller_Response_Abstract
	  * @return Celsus_State
	  */
	public function setResponse($response) {
		$this->_response = $response;
		return $this;
	}

	/**
	  * @return Zend_Controller_Request_Abstract
	  */
	public function getRequest() {
		if (null === $this->_request) {
			$this->_request = new Celsus_Controller_Request_Http();
		}
		return $this->_request;
	}

	/**
	  * @param Zend_Controller_Request_Abstract
	  * @return Celsus_State
	  */
	public function setRequest($request) {
		$this->_request = $request;
		return $this;
	}

	/**
	 * @todo This shouldn't live here.
	 */
	public function hasIdentity() {
		$auth = Celsus_Auth::getInstance();
		return $auth->hasIdentity();
	}

	/**
	 * @todo This shouldn't live here.
	 */
	public function clearIdentity() {
		$auth = Celsus_Auth::getInstance();
		return $auth->clearIdentity();
	}

	public function isAdmin() {
		// @todo Test to see if the current user is an admin, or else if some kind of side-door variable is set.
	}


}