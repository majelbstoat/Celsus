<?php
class Celsus_Controller_Response_Http extends Zend_Controller_Response_Http {

	protected $_strategy = null;

	protected $_viewModel = null;

	public function getViewModel() {
		return $this->_viewModel;
	}

	/**
	 * @return Celsus_Controller_Response_Http
	 */
	public function setViewModel($viewModel) {
		$this->_viewModel = $viewModel;
		return $this;
	}

	public function getStrategy() {
		return $this->_strategy;
	}

	/**
	 * @return Celsus_Controller_Response_Http
	 */
	public function setStrategy($strategy) {
		$this->_strategy = $strategy;
		return $this;
	}
}
