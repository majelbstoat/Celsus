<?php

abstract class Celsus_Controller_Processor implements Celsus_Controller_Processor_Interface {

	/**
	 * The action controller we are processing for.
	 *
	 * @var Celsus_Controller_Common $_actionController
	 */
	protected $_actionController = null;

	public function __construct(Celsus_Controller_Common $actionController) {
		$this->_actionController = $actionController;
	}

	/**
	 * Returns the view for the current controller.
	 *
	 * @return Zend_View_Abstract
	 */
	public function getView() {
		return $this->getController()->view;
	}

	public function getController() {
		return $this->_actionController;
	}

}