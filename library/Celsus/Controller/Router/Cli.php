<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Cli.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Simple Cli Router which does nothing, but is required to implement the router interface.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Router_Cli implements Zend_Controller_Router_Interface {

	public function route(Zend_Controller_Request_Abstract $dispatcher) {}

	public function assemble($userParams, $name = null, $reset = false, $encode = true) {}

	public function getFrontController() {}

	public function setFrontController(Zend_Controller_Front $controller) {}

	public function setParam($name, $value) {}

	public function setParams(array $params) {}

	public function getParam($name) {}

	public function getParams() {}

	public function clearParams($name = null) {}

}
