<?php

class Celsus_Controller_Processor_Form extends Celsus_Controller_Processor {

	const DEFAULT_SCRIPT_PATH = '/common';

	protected $_form = null;

	protected $_record = null;

	protected $_redirect = null;

	public function getData() {
		// Nice and simple, just uses HTTP POST data.
		return $_POST;
	}

	public function getScriptPath() {
		return self::DEFAULT_SCRIPT_PATH;
	}

	public function record(Celsus_Model $record) {
		$service = $this->_actionController->getService();
		$description = $service::getDescription($record);

		// Create and populate a record.
		$recordClass = str_replace('Model_Service', 'Record', $service);
		$this->_record = new $recordClass();
		$this->_record->populate($record->toArray());
		$this->getView()->record = $this->_record;
		$this->getView()->recordTitle = $description;
		$this->getView()->headTitle()->append($description);
	}

	public function template(Celsus_Model $record) {
		$this->_generateForm($record);
	}

	public function success(Celsus_Model $record) {
		$service = $this->_actionController->getService();
		$title = $service::getTitle();
		$description = $service::getDescription($record);
		Celsus_Feedback::add(Celsus_Feedback::INFO, "$title $description successfully saved!");
		$location = $this->_actionController->getRequest()->getControllerName() . "/$record->id/";
		Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrl($location);
	}

	public function error(Celsus_Model $record, $message) {
		$this->_fail($record, "There was an error saving your data. $message");
	}

	public function invalid(Celsus_Model $record) {
		$this->_fail($record, "One or more fields were invalid.  Please check your data and try again.");
	}

	/**
	 * Can't do lookups in a form context.
	 */
	public function lookup() {
		$location = $this->_actionController->getRequest()->getControllerName() . "/";
		Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrl($location);
	}

	protected function _fail(Celsus_Model $record, $message) {
		Celsus_Feedback::add(Celsus_Feedback::ERROR, $message);
		$this->_generateForm($record, true);
	}

	protected function _generateForm($record, $validate = false) {
		$service = $this->_actionController->getService();
		$editing = $record->id ? true : false;
		if (null === $this->_form) {
			$formClass = str_replace('Model_Service', 'Form', $service);
			$this->_form = new $formClass(array(
				'editing' => $editing
			));
		}

		if ($editing) {
			$action = 'edit';
			$title = 'Edit';
			$this->getView()->headTitle()->append($service::getDescription($record));
		} else {
			$action = 'new';
			$title = 'New';
		}

		$this->_form->populate($record->toArray());

		$this->getView()->headTitle()->append($title);
		$this->getView()->form = $this->_form;

		$path = $this->getScriptPath();
		Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')
			->setScriptAction('input')
			->setViewScriptPathSpec("$path/:action.phtml");

		return $this;
	}
}