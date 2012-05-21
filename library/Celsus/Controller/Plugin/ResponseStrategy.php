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
 * @package Celsus_Controller
 */
class Celsus_Controller_Plugin_ResponseStrategy extends Zend_Controller_Plugin_Abstract {

	protected $_contexts = array();

	protected $_aliases = array();

	public function routeShutdown(Zend_Controller_Request_Abstract $request) {

		$route = $request->getRoute();
		foreach ($this->_contexts as $context => $resolver) {
			if ($resolver::resolve($request)) {

				// The resolver matched this context, so check that it is valid for the given endpoint.
				if (!$route->hasContext($context)) {
					if (isset($this->_aliases[$context])) {
						$alias = $this->_aliases[$context];
						if (!$route->hasContext($alias)) {
							// The requested context is not available for this endpoint.
							throw new Celsus_Exception("Requested route is not valid", Celsus_Http::NOT_FOUND);
						}
					}
				}

				$request->setContext($context);
				return;
			}
		}

		throw new Celsus_Exception("Context could not be determined!", Celsus_Http::BAD_REQUEST);
	}

	public function postDispatch(Zend_Controller_Request_Abstract $request) {
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
