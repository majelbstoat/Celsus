<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: ContextDetection.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Adds support for context detection via a custom header.
 *
 * @category Celsus
 * @package Celsus_Service
 */
class Celsus_Service_ResponseManager {

	protected $_contexts = array();

	protected $_aliases = array();

	public function determineResponseStrategy(Celsus_State $state) {

		$request = $state->getRequest();

		foreach ($this->_contexts as $context => $resolver) {

			if ($resolver::resolve($request)) {
				$state->setContext($context);
				return;
			}
		}

		// @todo Figure out a way to process bad applications that don't specify a context.
		//throw new Celsus_Exception("Context could not be determined!", Celsus_Http::BAD_REQUEST);
	}

	public function verifyContext(Celsus_State $state) {

		$route = $state->getRoute();
		$context = $state->getContext();

		if (!$route->hasContext($context))  {
			// The resolver matched this context, so check that it is valid for the given endpoint.
			if (!isset($this->_aliases[$context]) || !$route->hasContext($this->_aliases[$context])) {
				// The requested context is not available for this endpoint.
				throw new Celsus_Exception("Requested route is not valid", Celsus_Http::NOT_FOUND);
			}

			// We're using an alias for this context.
			$context = $this->_aliases[$context];
		}

		$route->setSelectedContext($context);
	}

	public function postDispatch(Celsus_State $state) {
		$controllerName = $request->getControllerName();
		$actionName = $request->getActionName();
		$context = $request->getContext();
		$response = Zend_Controller_Front::getInstance()->getResponse();

		$class = ucfirst(APPLICATION_NAME) . '_Response_Strategy_' . ucfirst($controllerName) . '_' . ucfirst($context);

		$config = array(
			'request' => $request,
			'response' => $response,
		);

		$strategy = new $class($config);
		$responseMethod = $actionName . 'Response';

		$strategy->$responseMethod($response->getViewModel());
	}

	public function addContext($context, $resolver) {
		$this->_contexts[$context] = $resolver;
		return $this;
	}

	public function addAlias($key, $alias) {
		// We actually store this flipped, because we will be looking up by alias.
		$this->_aliases[$alias] = $key;
		return $this;
	}

}
