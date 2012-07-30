<?php

/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

require_once 'Zend/Controller/Request/Http.php';

/**
 * Defines a request object that is populated from an HTTP request.
 *
 * @category Celsus
 * @ingroup Celsus_Controller
 */
class Celsus_Controller_Request_Http extends Zend_Controller_Request_Http {

	protected $_route = null;

	protected $_allowedParams = null;

	protected $_context = null;

	protected $_error = null;

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

	public function getContext() {
		return $this->_context;
	}

	public function setContext($context) {
		$this->_context = $context;
		return $this;
	}

	public function setError($error) {
		$this->_error = $error;
		return $this;
	}

	public function getError() {
		return $this->_error;
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

	/**
	 * Returns the route associated with this request.
	 *
	 * @return Celsus_Route
	 */
	public function getRoute() {
		if (null === $this->_route) {
			// By default, get the route that was selected by the router.
			$this->setRoute(Zend_Controller_Front::getInstance()->getRouter()->getSelectedRoute());
		}
		return $this->_route;
	}

	public function setRoute(Celsus_Route $route) {
		$this->_route = $route;
		return $this;
	}

	/**
	 * Returns the parameters that are allowed for the current route.
	 *
	 * @return array
	 */
	protected function getAllowedParams() {
		if (null === $this->_allowedParams) {
			$route = $this->getRoute();

			$this->_allowedParams = $route->getParameters();
		}

		return $this->_allowedParams;
	}
}
