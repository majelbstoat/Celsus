<?php

class Celsus_Controller_Router extends Zend_Controller_Router_Abstract {

	const URI_DELIMITER = '/';
	const NAMED_PARAMETER_PREFIX = ':';

	protected $_selectedRoute = null;

	protected $_selectedRouteName = null;

	public function route(Zend_Controller_Request_Abstract $request) {

		$path = trim($request->getPathInfo(), self::URI_DELIMITER);

		$routeName = Celsus_Routing::getRouteNameByPath($path);
		if (!$routeName) {
			throw new Celsus_Exception('No route matched the request', Celsus_Http::NOT_FOUND);
		}

		$routeDefinition = Celsus_Routing::getRouteByName($routeName);
		$method = strtolower($request->getMethod());

		if (!$routeDefinition->methods->$method) {
			throw new Celsus_Exception("Route does not allow $method", Celsus_Http::METHOD_NOT_ALLOWED);
		}

		// @todo Test that this is valid for the context.
		// @todo Test that this operation is permitted.
		// @todo Set the parameters on the request from _GET and _POST

		$actionDefinition = $routeDefinition->methods->$method;
		$parameters = Celsus_Routing::extractRouteParametersFromPath($routeDefinition, $path);

		$request->setControllerName($routeDefinition->controller)
			->setActionName($actionDefinition->action)
			->setParams($parameters);

		$this->_selectedRouteName = $routeName;
		$this->_selectedRoute = $routeDefinition;
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