<?php

abstract class Celsus_Response_Strategy {

	/**
	 * The view model used for the response
	 *
	 * @var Celsus_View_Model
	 */
	protected $_viewModel = null;

	/**
	 * The state of the application
	 *
	 * @var Celsus_State
	 */
	protected $_state = null;

	public function __construct(Celsus_State $state) {

		// Construct a new parent view model, which defines the layout.
		$this->_state = $state;
		$this->_viewModel = new Celsus_View_Model();

		// Offer chance to do strategy-specific initialisation.
		$this->_init();
	}

	/**
	 * @return Celsus_State
	 */
	public function getState() {
		return $this->_state;
	}

	/**
	 * @param Celsus_State
	 * @return Celsus_Response_Strategy
	 */
	public function setState(Celsus_State $state) {
		$this->_state = $state;
		return $this;
	}

	/**
	 * @return Celsus_View_Model
	 */
	public function getViewModel() {
		return $this->_viewModel;
	}

	/**
	 * @param Celsus_View_Model
	 * @return Celsus_Response_Strategy
	 */
	public function setViewModel(Celsus_View_Model $viewModel) {
		$this->_viewModel = $viewModel;
		return $this;
	}

	protected function _init() {
	}

}