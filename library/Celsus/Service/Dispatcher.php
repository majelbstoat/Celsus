<?php

class Celsus_Service_Dispatcher {

	const ROUTE_UNAUTHORISED = 'auth_login';
	const ROUTE_ERROR = 'error';

	public function dispatch(Celsus_State $state) {

		// If we aren't authorised to dispatch this route, show the unauthorised route instead.
		if (!$state->authorised()) {
			$this->_alterRoute($state, self::ROUTE_UNAUTHORISED);
		}

		$request = $state->getRequest();

		while (!$request->isDispatched()) {

			if ($state->hasException()) {
				$this->_alterRoute($state, self::ROUTE_ERROR);
			}

			// Get the route.
			$route = $state->getRoute();

			$controllerName = ucfirst(APPLICATION_NAME) . '_Controller_' . ucfirst($route->getController());
			$action = $route->getAction() . 'Action';

			$controller = new $controllerName($state);

			$request->setDispatched(true);
			try {

				// Execute the controller action.
				$data = $controller->$action($state->getParameters());

				$responseModel = $controller->getResponseModel();

				if ($data) {
					$responseModel->setData($data);
				}

				// Set the response model.
				$state->setResponseModel($responseModel);

				// Respond to the request.
				$this->marshalResponse($state);

			} catch (Celsus_Exception $exception) {
				if (self::ROUTE_ERROR === $route->getName()) {
					var_dump("Error in the error handler?");
					var_dump($exception);
					die;
				} else {
					$state->setException($exception);
					$request->setDispatched(false);
				}
			}
		}
	}

	/**
	 * Determines the correct response strategy and executes it, using the result to
	 * populate a view model on the state object.
	 *
	 * @param Celsus_State $state
	 */
	public function marshalResponse(Celsus_State $state) {

		$route = $state->getRoute();
		$responseModel = $state->getResponseModel();

		// Determine the responding strategy class and method.
		$class = ucfirst(APPLICATION_NAME) . '_Response_Strategy_' . ucfirst($route->getController()) . '_' . ucfirst($route->getAction()) . '_' . ucfirst($state->getContext());
		$responseMethod = $responseModel->getResponseType() . 'Response';

		// Run the strategy to populate the view model or a redirection.
		$strategy = new $class($state);
		$strategy->$responseMethod($responseModel);

		$state->setViewModel($strategy->getViewModel());
	}

	/**
	 * Populates parameters supplied by the client.
	 * @param Celsus_State $state
	 */
	public function populateParameters(Celsus_State $state)
	{
		$route = $state->getRoute();
		$request = $state->getRequest();

		$routeParameters = $route->getParameters();

		$path = $request->getPathInfo();
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
			} elseif (isset($additionalParameters[$parameter])) {
				// Use the parameter that was set from the request.
				$parameters[$parameter] = $additionalParameters[$parameter];
			} elseif (isset($characteristics['default'])) {
				$parameters[$parameter] = $characteristics['default'];
			}

			if (!isset($parameters[$parameter]) && !isset($characteristics['optional'])) {
				throw new Celsus_Exception("Missing required parameter $parameter", Celsus_Http::BAD_REQUEST);
			}

		}

		$state->setParameters(new Celsus_Data_Object($parameters));
	}

	/**
	 * Alters the requested route to the specified one, which can happen
	 * when the user is unauthorised, or there is an error.
	 *
	 * @param Celsus_State $state
	 * @param string $routeName
	 */
	protected function _alterRoute(Celsus_State $state, $routeName) {
		$route = Celsus_Routing::getRouteByName($routeName);
		$route->setSelectedMethod(Celsus_Http::GET)->setSelectedContext($state->getContext());

		$state->setRoute($route);
	}
}