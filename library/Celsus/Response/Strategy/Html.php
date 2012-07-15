<?php

abstract class Celsus_Response_Strategy_Html extends Celsus_Response_Strategy {

	const CONFIG_METADATA = 'pageMetadata';

	protected $_layout = 'layouts/layout.phtml';

	protected $_rendererClass = 'Celsus_View_Renderer_Php';

	protected $_pageTitle = null;

	protected function _init() {

		$renderer = new $this->_rendererClass();
		$renderer->setTemplate($this->_layout);
		$this->_viewModel
			->setRenderer($renderer);
	}

	/**
	 * The default response is just to render the defined page.
	 *
	 * @param Celsus_State $state
	 */
	public function defaultResponse() {
		$this->_show();
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
	 * Given a template and some data, creates a child view model that can be rendered inside
	 * the main layout.
	 *
	 * If $template or $data aren't supplied, defaults are set from the route and response model
	 * respectively.
	 *
	 * @param string $template
	 * @param array $data
	 * @throws Celsus_Exception
	 */
	protected function _show($template = null, array $data = null) {

		// Use the specified template or the template defined on the route.
		if (null === $template) {
			$template = $this->_state->getRoute()->getContextConfiguration('template');
		}

		// If we still don't have template, we can't show anything.
		if (null === $template) {
			throw new Celsus_Exception("Missing template", Celsus_Http::INTERNAL_SERVER_ERROR);
		}

		// Create a new renderer.
		$renderer = new Celsus_View_Renderer_Php();
		$renderer->setTemplate($template);

		// Use the supplied data, or the data on the response model.
		if (null === $data) {
			$data = $this->_state->getResponseModel()->getData();
		}

		$childModel = new Celsus_View_Model();
		$childModel->setRenderer($renderer)->setData($data);

		$this->_viewModel->setChild('content', $childModel);

		$this->_initialiseMetadata();
	}

	/**
	 * Sets a service specific title if necessary and possible.
	 */
	protected function _initialiseMetadata() {

		$metadata = Celsus_I18n::getMessages('pageMetadata');

		$routeName = $this->_state->getRoute()->getName();
		$routeMetadata = $metadata->$routeName;

		$title = vsprintf($routeMetadata->title, $this->_titleComponents());

		Celsus_View_Helper_Broker::getHelper('pageTitle')->prepend(array(
			$title,
			$this->_state->getConfig()->applicationTitle
		));
	}

	protected function _titleComponents() {
		return array();
	}
}