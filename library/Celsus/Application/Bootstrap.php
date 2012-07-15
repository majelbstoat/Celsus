<?php

class Celsus_Application_Bootstrap {

	/**
	 * The services to exclude when bootstrapping.
	 *
	 * @var array
	 */
	protected $_excludedServices = array();

	/**
	 * The services that have already been bootstrapped.
	 *
	 * @var array $_bootstrappedServices
	 */
	protected $_bootstrappedServices = array();

	/**
	  * Application
	  *
	  * @var Celsus_Application
	  */
	protected $_application = null;

	/**
	 * The state of the application.
	 *
	 * @var Celsus_State $_state
	 */
	protected $_state;

	/**
	 * The service manager which will control the handling process.
	 *
	 * @var Celsus_Service_Manager $_serviceManager
	 */
	protected $_serviceManager = null;

	public function __construct(Celsus_Application $application) {
		$this->_application = $application;

		// Create a new state object to be passed through the execution pipeline.
		$this->_state = $application->getState();

		// Create a service manager to manage the execution pipeline.
		$this->_serviceManager = Celsus_Service_Manager::getInstance()->setState($this->_state);
	}

	/**
	  * @return Celsus_Application
	  */
	public function getApplication() {
		return $this->_application;
	}

	public function getExcludedServices() {
		return $this->_excludedServices;
	}

	/**
	 * Sets the resources that should be excluded when bootstrapping.
	 * @return Celsus_Application_Bootstrap_Bootstrap
	 */
	public function setExcludedServices(array $excludedServices) {
		$this->_excludedServices = $excludedServices;
		return $this;
	}

	/**
	 * Bootstraps the application, and allows for excluding of services.
	 *
	 * Circular dependencies are by design not handled, so don't introduce them.
	 *
	 * @param null|string|array $services
	 */
	public function bootstrap($services = null) {
		if (is_null($services)) {
			// We want to bootstrap all the services.
			$services = $this->_excludedServices ? array_diff($this->_services, $this->_excludedServices) : $this->_services;
		}

		// Ensure we have an array of services.
		if (!is_array($services)) {
			$services = array($services);
		}

		// Don't bootstrap something twice.
		$services = array_diff($services, $this->_bootstrappedServices);

		foreach ($services as $service) {
			$method = '_init' . ucfirst($service);
			$this->$method();

			// Remember that we've bootstrapped this.
			$this->_bootstrappedServices[] = $service;
		}

		return $this;
	}

	/**
	 * Run the application
	 */
	public function run() {
		return $this->_serviceManager->handle();
	}
}
