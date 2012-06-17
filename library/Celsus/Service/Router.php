<?php

class Celsus_Service_Router {

	const URI_DELIMITER = '/';
	const NAMED_PARAMETER_PREFIX = ':';

	protected $_selectedRoute = null;

	protected $_selectedRouteName = null;

	public function route(Celsus_State $state) {

		$request = $state->getRequest();

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

		$state->setRoute($route);
	}
}