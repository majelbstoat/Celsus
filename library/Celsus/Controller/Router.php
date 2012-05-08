<?php

class Celsus_Controller_Router extends Zend_Controller_Router_Abstract {

	const URI_DELIMITER = '/';
	const NAMED_PARAMETER_PREFIX = ':';

	protected $_selectedRoute = null;

	protected $_selectedRouteName = null;

	public function route(Zend_Controller_Request_Abstract $request) {

		$path = trim($request->getPathInfo(), self::URI_DELIMITER);

		$pathComponents = explode(self::URI_DELIMITER, $path);

//		Celsus_Log::info("Routing");
		foreach (Celsus_Routing::getRoutes() as $name => $routeDefinition) {

			// Test to see if the endpoint matches.
			$params = $this->_match($routeDefinition->route, $pathComponents);

			if (false !== $params) {

				// Endpoint matched.  Now test to see if the method is valid.
				$method = strtolower($request->getMethod());
				if ($routeDefinition->methods->$method) {

					// @todo Test that this is valid for the context.
					// @todo Test that this operation is permitted.

					// Method is valid for this endpoint, and we have a match.
					$actionDefinition = $routeDefinition->methods->$method;

					// Set the appropriate request data.
					$request->setControllerName($routeDefinition->controller)
						->setActionName($actionDefinition->action)
						->setParams($params);

					$this->_selectedRouteName = $name;

					return $request;
				}
			}
		}

		// No matching route was found.
		throw new Zend_Controller_Router_Exception('No route matched the request', Celsus_Http::NOT_FOUND);
	}

	/**
	 *
	 * @param string $route
	 * @param array $pathComponents
	 * @return boolean|array
	 */
	protected function _match($route, $pathComponents) {
		$params = array();
		$routeComponents = explode(self::URI_DELIMITER, trim($route));

		foreach ($routeComponents as $component) {
			$pathComponent = array_shift($pathComponents);

			if (self::NAMED_PARAMETER_PREFIX == substr($component, 0, 1)) {
				// Save a named parameter.
				$params[substr($component, 1)] = $pathComponent;
			} elseif ($component != $pathComponent) {
				// This wasn't a named parameter and the text didn't match, so this route fails.
				return false;
			}
		}

		return $params;
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
		return Celsus_Routing::getRoute($this->getSelectedRouteName());
	}

}