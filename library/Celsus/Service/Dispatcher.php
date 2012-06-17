<?php

class Celsus_Service_Dispatcher {

	public function dispatch(Celsus_State $state) {

		$request = $state->getRequest();

		while (!$request->isDispatched()) {

			if ($state->hasException()) {
				$this->_handleException($state);
			}

			// Get the route.
			$route = $state->getRoute();

			$controllerName = 'Shinnen_Controller_' . ucfirst($route->getController());
			$action = $route->getAction() . 'Action';

			$controller = new $controllerName();

			$request->setDispatched(true);
			try {
				$controller->$action($state);
			} catch (Celsus_Exception $exception) {
				if ($state->hasException()) {
					var_dump("Error in the error handler?");
					die;
				} else {
					$state->setException($exception);
					$request->setDispatched(false);
				}
			}
		}

	}

	protected function _handleException(Celsus_State $state) {

		$exception = $state->getException();
		$code = $exception->getCode();

		switch ($code) {
			case Celsus_Http::UNAUTHORISED:
				$routeName = 'auth_login';
				break;

			default:
				$routeName = 'error';
				break;
		}

		// Change the route to the correct handler.
		// @todo Keep the old route around so we can see what went wrong.
		$route = Celsus_Routing::getRouteByName($routeName);
		$route->setSelectedMethod('get')
			->setSelectedContext($state->getContext());

		$state->setRoute($route);
	}

	/**
	 * Populates parameters supplied by the client.
	 * @param Celsus_State $state
	 */
	public function populateParameters(Celsus_State $state)
	{
		$route = $state->getRoute();
		var_dump($route);

		$routeParameters = $route->getParameters();

		$pathParameters = $route->extractParametersFromPath($path);

		// @todo Merge with default parameters from the route config.

		$request = $state->getRequest();
		if ($request->isDelete() || $request->isPut() || $request->isPost()) {
			$additionalParameters = array();
			parse_str($request->getRawBody(), $additionalParameters);
		} elseif ($request->isGet()) {
			$additionalParameters = $request->getQuery();
		}

		$parameters = array();

		foreach ($routeParameters as $parameter => $characteristics) {

			if (isset($pathParameters[$parameter])) {
				// Use the parameter that was set from the URL path.
				$parameters[$parameter] = $pathParameters[$parameter];
			} elseif (isset($additionalParameters[$parameters])) {
				// Use the parameter that was set from the request.
				$parameters[$parameter] = $additionalParameters[$parameter];
			} elseif (isset($characteristics['default'])) {
				$parameters[$parameter] = $characteristics['default'];
			}

			if (!isset($parameters[$parameter]) && !isset($characteristics['optional'])) {
				throw new Celsus_Exception("Missing required parameter $parameter", Celsus_Http::PRECONDITION_FAILED);
			}

		}


		$state->setParameters(array_merge($pathParameters, $additionalParameters));
	}

}