<?php

class Celsus_Controller_Processor_Json extends Celsus_Controller_Processor {

	public function getData() {
		// Convert supplied JSON data into a useable form.
		$rawBody = $this->_actionController->getRequest()->getRawBody();
		return Zend_Json::decode($rawBody);
	}

	public function success(Celsus_Model $record) {
		$this->_actionController->view->record = $record->toArray();
	}

	public function template(Celsus_Model $record) {
		$data = $record->toArray();

		// We don't include the id column in skeletons.
		unset($data['id']);
		$this->_actionController->view->template = $data;
	}

	public function record(Celsus_Model $record) {
		$this->_actionController->view->record = $record->toArray();
	}

	public function invalid(Celsus_Model $record) {
		$this->_actionController->view->errors = $record->getValidationMessages();
	}

	public function error(Celsus_Model $record) {}

	/**
	 * Returns data in a format suitable for using as a lookup on the client-side.
	 * Uses POST data to filter, if available.
	 */
	public function lookup() {
		$service = $this->_actionController->getService();
		$this->_actionController->view->data = $service::getLookupValues($_POST);
	}

}