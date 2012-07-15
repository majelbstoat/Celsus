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

	/**
	 * Determines whether the response should be returned or echoed.
	 *
	 * @var boolean
	 */
	protected $_returnResponse = null;

	public function determineContext(Celsus_State $state) {

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

	public function respond(Celsus_State $state) {

		// Render the view model
		$response = $state->getResponse();

		if (!$response->isRedirect()) {
			$response->appendBody($state->getViewModel()->render());
		}

		if ($this->returnResponse()) {
			return $state;
		} else {
			// Send the headers.
        	$response->sendHeaders();

        	// Echo the response.
			$response->outputBody();
		}
	}

	/**
	  * Determines whether the response should be returned or echoed.
	  *
	  * @param boolean
	  * @return boolean|Celsus_Service_ResponseManager
	  */
	public function returnResponse($returnResponse = null) {
		if (null === $returnResponse) {
			return $this->_returnResponse;
		} else {
			$this->_returnResponse = $returnResponse;
			return $this;
		}
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
