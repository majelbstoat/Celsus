<?php

class Celsus_Service_Manager {

	protected static $_instance = null;

	protected $_dispatcher = null;

	protected $_router = null;

	protected $_responseManager = null;

	/**
	  * The object which determines whether the client is authenticated for the request.
	  *
	  * @var Celsus_Service_AuthenticationManager
	  */
	protected $_authenticationManager = null;

	/**
	  * @return Celsus_Service_AuthenticationManager
	  */
	public function getAuthenticationManager() {
		if (null === $this->_authenticationManager) {
			$this->_authenticationManager = new Celsus_Service_AuthenticationManager();
		}
		return $this->_authenticationManager;
	}

	/**
	  * @param Celsus_Service_AuthenticationManager
	  * @return Celsus_Service_Manager
	  */
	public function setAuthenticationManager(Celsus_Service_AuthenticationManager $authenticationManager) {
		$this->_authenticationManager = $authenticationManager;
		return $this;
	}


	/**
	 * @var Celsus_State
	 */
	protected $_state = null;

	public function handle() {

		$responseManager = $this->getResponseManager();

		$dispatcher = $this->getDispatcher();

		try {
			// Determine how to respond.
			$responseManager->determineContext($this->_state);

			// Determine the correct route to load.
			$this->getRouter()->route($this->_state);

			// Confirm that the requested context is valid for the chosen route.
			$responseManager->verifyContext($this->_state);

			// Test the identity of the user, if the route requires it.
			$this->getAuthenticationManager()->authenticate($this->_state);

			// Populate route parameters.
			$dispatcher->populateParameters($this->_state);

		} catch (Celsus_Exception $exception) {
			$this->_state->setException($exception);
		}

		// Dispatch the request.
		$dispatcher->dispatch($this->_state);

		$responseManager->respond($this->_state);
	}

	/**
	 * Gets the response
	 * @return Celsus_Service_ResponseManager
	 */
	public function getResponseManager() {
		if (null === $this->_responseManager) {
			$this->_responseManager = new Celsus_Service_ResponseManager();
		}
		return $this->_responseManager;
	}

	/**
	 * @return Celsus_Service_Manager
	 */
	public function setResponseManager(Celsus_Service_ResponseManager $responseManager) {
		$this->_responseManager = $responseManager;
		return $this;
	}

	/**
	 * @return Celsus_Service_Router
	 */
	public function getRouter() {
		if (null === $this->_router) {
			$this->_router = new Celsus_Service_Router();
		}
		return $this->_router;
	}

	/**
	 * @return Celsus_Service_Manager
	 */
	public function setRouter($router) {
		$this->_router = $router;
		return $this;
	}

	/**
	 * @return Celsus_Service_Dispatcher
	 */
	public function getDispatcher() {
		if (null === $this->_dispatcher) {
			$this->_dispatcher = new Celsus_Service_Dispatcher();
		}
		return $this->_dispatcher;
	}

	/**
	 * @return Celsus_Service_Manager
	 */
	public function setDispatcher($dispatcher) {
		$this->_dispatcher = $dispatcher;
		return $this;
	}

	/**
	 * @return Celsus_Service_Manager
	 */
	public function setState(Celsus_State $state) {
		$this->_state = $state;
		return $this;
	}

	/**
	 * @return Celsus_State
	 */
	public function getState() {
		return $this->_state;
	}

	protected function __construct() {}

	protected function __clone() {}

	/**
	 * @return Celsus_Service_Manager
	 */
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new static();
		}
		return self::$_instance;
	}


}