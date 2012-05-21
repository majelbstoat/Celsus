<?php

abstract class Celsus_Response_Strategy {

	/**
	 * The renderer.
	 *
	 * @var Celsus_Controller_Request_Http $_request
	 */
	protected $_renderer = null;

	/**
	 * The response object, containing headers and return data.
	 *
	 * View models can contextually inject headers.
	 *
	 * @var Zend_Controller_Response_Abstract $_response
	 */
	protected $_response = null;

	/**
	 * The request object, containing request information.
	 *
	 * View models may base their strategy on a request.
	 *
	 * @var Celsus_Controller_Request_Http $_request
	 */
	protected $_request = null;

	/**
	 * The service associated with this view model.
	 *
	 * @var Celsus_Model_Service $_service
	 */
	protected $_service = null;

	// Helper functions

	public function __set($key, $value) {
		$this->_data[$key] = $value;
	}

	public function __get($key) {
		if (!isset($this->_data[$key])) {
			return null;
		}
		return $this->_data[$key];
	}

	public function getData() {
		return $this->_data;
	}

	public function setChild($name, Celsus_View_Model $child) {
		$this->_children[$name] = $child;
		return $this;
	}

	public function getChildren() {
		return $this->_children;
	}

	public function hasChildren() {
		return !!$this->_children;
	}

	/**
	 * @return Celsus_View_Model
	 */
	public function setChildren(array $children) {
		foreach ($children as $name => $child) {
			$this->setChild($name, $child);
		}
		return $this;
	}

	public function getRequest() {
		return $this->_request;
	}

	/**
	 * @return Celsus_View_Model
	 */
	public function setRequest($request) {
		$this->_request = $request;
		return $this;
	}

	public function getResponse() {
		return $this->_response;
	}

	/**
	 * @return Celsus_View_Model
	 */
	public function setResponse($response) {
		$this->_response = $response;
		return $this;
	}

	public function getService() {
		return $this->_service;
	}

	public function setRenderer($renderer) {
		$this->_renderer = $renderer;
		return $this;
	}

	/**
	 * @return Celsus_View_Model
	 */
	public function setService($service) {
		$this->_service = $service;
		return $this;
	}

	public function getTemplate() {
		return $this->_template;
	}

	/**
	 * @return Celsus_View_Model
	 */
	public function setTemplate($template) {
		$this->_template = $template;
		return $this;
	}

	public function getView() {
		return $this->_view;
	}

	/**
	 * @return Celsus_View_Model
	 */
	public function setView($view) {
		$this->_view = $view;
		return $this;
	}
}