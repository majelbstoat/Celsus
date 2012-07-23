<?php

class Celsus_State {

	/**
	 * Whether or not the user is authorised for the requested route.
	 *
	 * @var boolean $_authorised
	 */
	protected $_authorised = true;

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
	 * The original route object
	 *
	 * @var Celsus_Route
	 */
	protected $_originalRoute = null;

	/**
	 * The set of parameters supplied by the client
	 *
	 * @var Celsus_Data_Object
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
	 * @return boolean
	 */
	public function hasContext() {
		return !!$this->_context;
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
	 * @param Celsus_Data_Object
	 * @return Celsus_State
	 */
	public function setParameters(Celsus_Data_Object $parameters) {
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
	public function setException(Celsus_Exception $exception) {
		$this->_exception = $exception;
		return $this;
	}

	/**
	 * Clears the exception on the state.
	 */
	public function clearException() {
		$this->_exception = null;
	}

	/**
	 * @return Celsus_Route
	 */
	public function getRoute() {
		return $this->_route;
	}

	/**
	 * Sets the route to use for the request.
	 *
	 * Also updates the original route on subsequent calls, to
	 * keep track of re-routing.
	 *
	 * @param Celsus_Route
	 * @return Celsus_State
	 */
	public function setRoute($route) {

		// Set the original route, if this is a re-route.
		if (null !== $this->_route && null === $this->_originalRoute) {
			$this->_originalRoute = $this->_route;
		}

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
	public function getIdentity() {
		$auth = Celsus_Auth::getInstance();
		return $auth->getIdentity();
	}

	/**
	 * @todo This shouldn't live here.
	 */
	public function clearIdentity() {
		$auth = Celsus_Auth::getInstance();
		return $auth->clearIdentity();
	}

	/**
	 * Determines whether a user is authorised.
	 *
	 * If a parameter is supplied, sets the flag.  If not, reads it.
	 *
	 * @param boolean $authorised
	 * @return boolean|Celsus_State
	 */
	public function authorised($authorised = null) {
		if (null === $authorised) {
			return $this->_authorised;
		} else {
			$this->_authorised = $authorised;
			return $this;
		}
	}
	public function isAdmin() {
		// @todo Test to see if the current user is an admin, or else if some kind of side-door variable is set.
	}


}