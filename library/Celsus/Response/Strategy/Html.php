<?php

abstract class Celsus_Response_Strategy_Html extends Celsus_Response_Strategy {

	const CONFIG_METADATA = 'pageMetadata';

	protected $_layout = 'layouts/layout.phtml';

	protected $_rendererClass = 'Celsus_View_Renderer_Php';

	protected $_pageTitle = null;

	protected function _init() {

		$renderer = new $this->_rendererClass();
		$renderer->setTemplate($this->_layout);
		$this->_viewModel->setRenderer($renderer);
	}

	public function redirectResponse(Celsus_Response_Model $responseModel) {
		$this->_redirect($this->_state->getResponseModel()->location);
	}

	/**
	 * Redirects the user to another URL.
	 *
	 * @param Celsus_State $state
	 */
	protected function _redirect($location, $code = Celsus_Http::FOUND) {
		$response = $this->_state->getResponse();
		$response->setRedirect($location, $code);
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

		// Use the specified template or the template defined on the route.
		$template = $config['template'] ?: $this->_state->getRoute()->getContextConfiguration('template');

		// If we still don't have template, we can't show anything.
		if (null === $template) {
			throw new Celsus_Exception("Missing template for " . $this->_state->getRoute()->getName(), Celsus_Http::INTERNAL_SERVER_ERROR);
		}

		// Use the supplied data, or the data on the response model.
		$data = $config['data'] ?: $this->_state->getResponseModel()->getData();

		// Create a new renderer.
		$renderer = new Celsus_View_Renderer_Php();
		$renderer->setTemplate($template);

		$childModel = new Celsus_View_Model();
		$childModel->setRenderer($renderer)->setData($data);

		$this->_viewModel->setChild('content', $childModel);

		$this->_populatePageMetadata($config['metadata']);
	}

	/**
	 * Sets a service specific title if necessary and possible.
	 */
	protected function _populatePageMetadata(array $metadata) {
		$metadataMessages = Celsus_I18n::getMessages('pageMetadata');

		$routeName = $this->_state->getRoute()->getName();
		$routeMetadataMessages = $metadataMessages->$routeName;

		// Set the title for the page.

		$titleComponents = isset($metadata['titleComponents']) ? $metadata['titleComponents'] : array();
		$title = vsprintf($routeMetadataMessages->title, $titleComponents);

		Celsus_View_Helper_Broker::getHelper('pageTitle')->prepend(array(
			$title,
			$this->_state->getConfig()->applicationTitle
		));

		// Set the meta tags for this page.
		$descriptionComponents = isset($metadata['descriptionComponents']) ? $metadata['descriptionComponents'] : array();
		$description = vsprintf($routeMetadataMessages->description, $descriptionComponents);

		$headHelper = Celsus_View_Helper_Broker::getHelper('head');

		$headHelper->setDescription($description)
			->setViewport();

		if (isset($metadata['pageScript'])) {
			$scriptConfig = isset($metadata['pageScriptConfig']) ? $metadata['pageScriptConfig'] : array();
			$headHelper->setScript($metadata['pageScript'], $scriptConfig);
		}
	}
}