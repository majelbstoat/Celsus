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

	public function __construct($prefix) {
		$this->_prefix = $prefix;
	}

	public function data() {
		if (null === $this->_mockData) {
			$this->_mockData = new Celsus_Test_Mock_Data_Broker($this->_prefix . 'Data_');
		}
		return $this->_mockData;
	}

	public function __call($method, $arguments) {
		if (!array_key_exists($method, $this->_mockGenerators)) {
			require_once('mocks/' . ucfirst($method) . '.php');

			$mockClass = $this->_prefix . ucfirst($method);
			$this->_mockGenerators[$method] = new $mockClass();
		}
		$generator = $this->_mockGenerators[$method];
		call_user_func_array(array($generator, 'mock'), $arguments);

		return $this;
	}

}