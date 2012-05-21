<?php

abstract class Celsus_Response_Strategy_Php extends Celsus_Response_Strategy {

	/**
	 * The template file used to render the response.
	 *
	 * @var string $_template
	 */
	protected $_template = null;

	/**
	 * The layout file used to render the response.
	 *
	 * @var string $_layout
	 */
	protected $_layout = null;

	/**
	 * The view which will eventually render this model.
	 *
	 * @var Zend_View $_view
	 */
	protected $_view = null;

	/**
	 * @return Celsus_View_Renderer_Php
	 */
	public function getRenderer() {
		if (null === $this->_renderer) {
			$this->_renderer = new Celsus_View_Renderer_Php();
		}
		return $this->_renderer;
	}

	public function init() {

		// If we have specified a service, add it to the page title.
		if ($this->_service) {
			$service = $this->_service;
			$title = $service::getTitle();
			$this->_view->headTitle()->append($title);
		}
	}
}