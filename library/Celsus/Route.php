<?php

class Celsus_Route {

	protected $_definition = null;

	protected $_name = null;

	protected $_selectedMethod = null;

	protected $_selectedContext = null;

	public function __construct($name, Zend_Config $definition) {
		$this->_definition = $definition;
		$this->_name = $name;
	}

	public function hasMethod($method) {
		return !!$this->_definition->methods->$method;
	}

	public function hasContext($context) {
		if (null === $this->_selectedMethod) {
			throw new Celsus_Exception("Can't determine context without a selected method", Celsus_Http::INTERNAL_SERVER_ERROR);
		}
		return $this->_selectedMethod->contexts && isset($this->_selectedMethod->contexts->$context);
	}

	public function setSelectedMethod($selectedMethod) {
		$selectedMethod = strtolower($selectedMethod);
		$this->_selectedMethod = $this->_definition->methods->$selectedMethod;
		return $this;
	}

	public function setSelectedContext($selectedContext) {
		$this->_selectedContext = $this->_selectedMethod->contexts->$selectedContext;
		return $this;
	}

	public function getController() {
		return $this->_definition->controller;
	}

	public function getAction() {
		return $this->_selectedMethod->action;
	}

	public function getName() {
		return $this->_name;
	}

	public function getParameters() {
		return $this->_selectedMethod->parameters ? $this->_selectedMethod->parameters->toArray() : array();
	}

	public function extractParametersFromPath($path) {
		$params = array();

		$path = Celsus_Routing::sanitisePath($path);

		$routeComponents = explode(Celsus_Routing::SYNTAX_DELIMITER, trim($this->_definition->route));
		$pathComponents = explode(Celsus_Routing::SYNTAX_DELIMITER, $path);

		$count = count($routeComponents);

		for ($i = 0; $i < $count; $i++) {
			if (Celsus_Routing::SYNTAX_NAME_PREFIX == substr($routeComponents[$i], 0, 1)) {
				$params[substr($routeComponents[$i], 1)] = $pathComponents[$i];
			}
		}

		return $params;
	}

	/**
	 * Determines whether this route requires that the client be authenticated.
	 *
	 * @return boolean
	 */
	public function requiresAuthentication() {
		return !!$this->getContextConfiguration('requiresAuthentication');
	}

	public function getContextConfiguration($field) {
		if (null === $this->_selectedContext) {
			throw new Celsus_Exception("Can't determine login requirement without a selected context", Celsus_Http::INTERNAL_SERVER_ERROR);
		}
		return isset($this->_selectedContext->$field) ? $this->_selectedContext->$field : null;
	}
}