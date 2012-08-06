<?php

abstract class Celsus_Response_Strategy_Api extends Celsus_Response_Strategy {

	protected $_rendererClass = 'Celsus_View_Renderer_Json';

	protected $_pageTitle = null;

	public function createdResponse(Celsus_Response_Model $responseModel) {
		$this->_state->getResponse()->setHttpResponseCode(Celsus_Http::CREATED);
	}

	protected function _init() {
		$renderer = new $this->_rendererClass();
		$this->_viewModel->setRenderer($renderer);
	}

	/**
	 * Given a config, creates a child view model that can be rendered inside
	 * the main layout.
	 *
	 * If a template or data aren't supplied in the config, defaults are set from the route and response model
	 * respectively.
	 *
	 * @param string $template
	 * @param array $data
	 * @throws Celsus_Exception
	 */
	protected function _show(array $config = array()) {
		$defaultConfig = array(
			'template' => null,
			'data' => array(),
			'metadata' => array()
		);

		$config = array_merge($defaultConfig, $config);

		// Use the supplied data, or the data on the response model.
		$data = $config['data'] ?: $this->_state->getResponseModel()->getData();

		$this->_viewModel->setData($data);
	}
}