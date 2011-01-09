<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Test
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
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

	public function __construct($prefix) {
		$this->_prefix = $prefix;
	}

	public function __call($method, $arguments) {
		include_once(ucfirst($method) . '.php');

		if (!array_key_exists($method, $this->_mockGenerators)) {
			$mockClass = $this->_prefix . ucfirst($method);
			$this->_mockGenerators[$method] = new $mockClass();
		}
		$generator = $this->_mockGenerators[$method];
		call_user_func_array(array($generator, 'mock'), $arguments);

		return $this;
	}

}