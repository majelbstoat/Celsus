<?php

/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

/**
 * Defines a request object that is populated from an HTTP request.
 *
 * @category Celsus
 * @ingroup Celsus_Controller
 */
class Celsus_Controller_Request_Http extends Zend_Controller_Request_Http {

	protected $_route = null;

	protected $_allowedParams = null;

	/**
	 * Set parameters
	 *
	 * Set one or more parameters. Only parameters matching the specific
	 * parameter list in the route are allowed.
	 *
	 * @param array $params
	 * @return Zend_Controller_Request_Http
	 */
	public function setParams(array $params)
	{
		$allowedParams = $this->getAllowedParams();
		foreach ($params as $key => $value) {
			$key = (string) $key;
			if (in_array($key, $allowedParams)) {
				$this->_params[$key] = $value;
			}
		}

		return $this;
	}

	protected function getAllowedParams() {
		if (null === $this->_allowedParams) {
			$route = $this->getRoute();
			$method = strtolower($this->getMethod());
			$actionDefinition = $route->methods->$method;

			$params = $actionDefinition->parameters ? $actionDefinition->parameters->toArray() : array();
			$params[] = Celsus_Error::ERROR_FLAG;

			$this->_allowedParams = $params;
		}

		return $this->_allowedParams;
	}

	/**
	 * Sets a single parameter.
	 *
	 * Only allows whitelisted parameters to be set.
	 */
	public function setParam($key, $value) {
		$allowedParams = $this->getAllowedParams();

		$key = (string) $key;

		if (in_array($key, $allowedParams)) {
			$this->_params[$key] = $value;
		}

		return $this;
	}

	public function getParams() {
		return $this->_params;
	}

	public function getParam($key, $default = null) {
		return isset($this->_params[$key]) ? $this->_params[$key] : $default;
	}

	public function getRoute() {
		if (null === $this->_route) {
			// By default, get the route that was selected by the router.
			$route = Zend_Controller_Front::getInstance()->getRouter()->getSelectedRoute();
		}
		return $this->_route;
	}

	public function setRoute(Zend_Config $route) {
		$this->_route = $route;
		return $this;
	}


}