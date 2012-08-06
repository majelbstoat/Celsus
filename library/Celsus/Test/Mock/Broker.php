<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Test
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Broker.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Decorator object that provides a gateway for mocking objects.
 *
 * @category Celsus
 * @package Celsus_Test
 */
class Celsus_Test_Mock_Broker {

	protected $_prefix = null;

	protected $_mockGenerators = array();

	protected $_mockData = null;

	protected $_enabled = true;

	public function __construct($prefix) {
		$this->_prefix = $prefix;
	}

	public function setEnabled($enabled) {
		$this->_enabled = $enabled;
	}

	/**
	 * Provides an interface to retrieving mock data objects.
	 *
	 * @return Celsus_Test_Mock_Data_Broker
	 */
	public function data() {
		if (null === $this->_mockData) {
			$this->_mockData = new Celsus_Test_Mock_Data_Broker($this->_prefix . 'Data_');
		}
		return $this->_mockData;
	}

	public function reset() {
		foreach ($this->_mockGenerators as $generator) {
			$generator->reset();
		}
		$this->_mockData = null;
		$this->_mockGenerators = array();
	}

	/**
	 * Proxies a call through to the actual mocking function.
	 *
	 * If mocking is not enabled, because we are integration testing for example, returns
	 * a reference to itself, so that chained method calls do not break, but does not
	 * actually mock anything.
	 *
	 * @param string $method
	 * @param array $arguments
	 */
	public function __call($method, $arguments) {

		// If mocking is not enabled, we don't want to mock any objects.
		if (!$this->_enabled) {
			return $this;
		}

		if (!array_key_exists($method, $this->_mockGenerators)) {
			require_once('mocks/' . ucfirst($method) . '.php');

			$mockClass = $this->_prefix . ucfirst($method);
			$this->_mockGenerators[$method] = new $mockClass($this);
		}

		return $this->_mockGenerators[$method];
	}

}