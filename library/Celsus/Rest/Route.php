<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Route.php 72M 2010-09-19 08:42:39Z (local) $
 */

/**
 * RESTful functionality.
 *
 * @defgroup Celsus_Rest Celsus Rest
 */

/**
 * REST-based routing.
 *
 * @ingroup Celsus_Rest
 */
class Celsus_Rest_Route extends Zend_Rest_Route {

	/**
	 * Matches a user submitted request. Assigns and returns an array of variables
	 * on a successful match.
	 *
	 * Matches the following example paths:
	 *	/clients/1/contracts/new/
	 *  /clients/1/contracts/
	 *  /clients/1/history/
	 *  /clients/new/
	 *  /clients/1/
	 *  /clients/
	 *
	 * @param Zend_Controller_Request_Http $request Request used to match against this routing ruleset
	 * @return array An array of assigned values or a false on a mismatch
	 */
	public function match($request, $partial = false) {
		if (!$request instanceof Zend_Controller_Request_Http) {
			$request = $this->_front->getRequest();
		}
		$this->_request = $request;
		$this->_setRequestKeys();

		$path   = $request->getPathInfo();
		$params = $request->getParams();
		$values = array();
		$path   = trim($path, self::URI_DELIMITER);
		if (!$path) {
			// Bail early if no parameters are specified.
			return false;
		}

		$moduleName  = $this->_defaults[$this->_moduleKey];
		$controllerName  = $this->_defaults[$this->_controllerKey];
		$actionName  = $this->_defaults[$this->_actionKey];

		$path = explode(self::URI_DELIMITER, $path);
		$dispatcher = $this->_front->getDispatcher();
		$requestMethod = strtolower($request->getMethod());

		// Determine Controller
		$controllerName = array_shift($path);
		$actionName = $requestMethod;

		switch (count($path)) {
			case 3:
				// Possible options are:
				// 	/controller/id/controller/new/ -> /clients/1/contracts/new (Structure for adding a contract to client 1).
				$parentControllerName = $controllerName;
				$controllerName = $path[1];

				// If we're POSTing to this URL, we're actually PUTing a new record.
				$actionName = (('new' == $path[2]) && ('post' == $requestMethod)) ?  'put' : $path[2];

				$parent = Celsus_Inflector::singularize($parentControllerName);
				$params['parent'] = array(
						'field' => $parent,
						'id' => $path[0]
				);
				break;

			case 2:
				// Possible patterns are:
				//	/controller/id/controller/ -> /countries/1/regions/ (The regions of country 1).
				//  /controller/id/action/ -> /countries/1/edit/ (Edit country 1)
				// By default, go to the primary controller on path[0] and assume it's an action on the controller.
				$actionName = (('edit' == $path[1]) && ('post' == $requestMethod)) ? 'post' : $path[1];

				$controllerPaths = $dispatcher->getControllerDirectory();
				$controllerFilePath =  $controllerPaths['default'] . DIRECTORY_SEPARATOR . $dispatcher->classToFilename(ucfirst($actionName) . 'Controller');
				if (file_exists($controllerFilePath)) {
					// This is a controller, so we actually want the index action on the child controller, filtered by the parent controller.
					$parentControllerName = $controllerName;
					$parent = Celsus_Inflector::singularize($parentControllerName);
					$params['parent'] = array(
						'field' => $parent,
						'id' => $path[0]
					);
					$controllerName = $actionName;
					$actionName = 'index';
				} else {
					$params['id'] = $path[0];
				}
				break;

			case 1;
				// Possible patterns are:
				// /controller/id/ -> /countries/1/ (Country 1)
				// /controller/action/ -> /countries/new/ (Structure for entering a new country).
				if ('new' == $path[0]) {
					// If we are POSTing to 'new', we are actually PUTting a new record.
					$actionName = ('post' == $requestMethod) ?  'put' : 'new';
				} elseif (ctype_digit($path[0])) {
					$params['id'] = $path[0];
				} else {
					$actionName = $path[0];
				}
				break;

			case 0;
				// Posibble patterns are:
				// /controller/
				$actionName = 'index';
				break;
		}

		$values = array(
			$this->_moduleKey => $moduleName,
			$this->_controllerKey => $controllerName,
			$this->_actionKey => $actionName
		);

		$this->_values = $values + $params;

		$result = $this->_values + $this->_defaults;

		return $result;
	}


	/**
	 * Sub-classed class returning a Celsus_Rest_Route instead of a Zend_Rest_Route.
	 */
	public static function getInstance(Zend_Config $config)
	{
		$frontController = Zend_Controller_Front::getInstance();
		$defaultsArray = array();
		$restfulConfigArray = array();
		foreach ($config as $key => $values) {
			if ($key == 'type') {
				// do nothing
			} elseif ($key == 'defaults') {
				$defaultsArray = $values->toArray();
			} else {
				$restfulConfigArray[$key] = explode(',', $values);
			}
		}
		$instance = new static($frontController, $defaultsArray, $restfulConfigArray);
		return $instance;
	}

}