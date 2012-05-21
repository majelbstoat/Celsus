<?php

class Celsus_Controller_Router extends Zend_Controller_Router_Abstract {

	const URI_DELIMITER = '/';
	const NAMED_PARAMETER_PREFIX = ':';

	protected $_selectedRoute = null;

	protected $_selectedRouteName = null;

	public function route(Zend_Controller_Request_Abstract $request) {

		$path = $request->getPathInfo();

		$route = Celsus_Routing::getRouteByPath($path);
		if (!$route) {
			throw new Celsus_Exception('No route matched the request', Celsus_Http::NOT_FOUND);
		}

		$method = strtolower($request->getMethod());

		if (!$route->hasMethod($method)) {
			throw new Celsus_Exception("Route does not allow $method", Celsus_Http::METHOD_NOT_ALLOWED);
		}

		$route->setSelectedMethod($method);

		// @todo Test that this operation is authorised.

		$parameters = $route->extractParametersFromPath($path);

		$this->_selectedRouteName = $route->getName();
		$this->_selectedRoute = $route;

		$request->setControllerName($route->getController())
			->setActionName($route->getAction())
			->setRoute($route)
			->setParams($parameters);
	}

	/**
	 * Generates a URL path that can be used in URL creation, redirection, etc.
	 *
	 * @param  array $userParams Options passed by a user used to override parameters
	 * @param  mixed $name The name of a Route to use
	 * @param  bool $reset Whether to reset to the route defaults ignoring URL params
	 * @param  bool $encode Tells to encode URL parts on output
	 * @throws Zend_Controller_Router_Exception
	 * @return string Resulting absolute URL path
	 */
	public function assemble($userParams, $name = null, $reset = false, $encode = true)
	{
		if (!$name) {
			$name = $this->getSelectedRouteName();
		}
		return Celsus_Routing::linkTo($name, $userParams);
	}

	public function getSelectedRouteName() {
		if (null === $this->_selectedRouteName) {
			throw new Celsus_Exception("Route has not been selected");
		}
		return $this->_selectedRouteName;
	}

	public function getSelectedRoute() {
		if (null === $this->_selectedRoute) {
			$this->_selectedRoute = Celsus_Routing::getRouteByName($this->getSelectedRouteName());
		}
		return $this->_selectedRoute;
	}


}