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
class Celsus_Test_Mock_Data_Broker {

	protected $_prefix = null;

	protected $_dataGenerators = array();

	public function __construct($prefix) {
		$this->_prefix = $prefix;
	}

	public function __call($dataType, $arguments) {
		if (!array_key_exists($dataType, $this->_dataGenerators)) {
			include_once('data/' . ucfirst($dataType) . '.php');

			$mockClass = $this->_prefix . ucfirst($dataType);
			$this->_dataGenerators[$dataType] = new $mockClass();
		}
		return $this->_dataGenerators[$dataType];
	}

}