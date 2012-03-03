<?php

class Celsus_Controller_Processor_Json extends Celsus_Controller_Processor {

	public function getData() {
		// Convert supplied JSON data into a useable form.
		$rawBody = $this->_actionController->getRequest()->getRawBody();
		return Zend_Json::decode($rawBody);
	}

	public function success(Celsus_Model $record) {
		$this->_actionController->view->result = 'ok';
		$this->_actionController->view->record = $record->toArray();
	}

	public function template(Celsus_Model $record) {
		$data = $record->toArray();

		// We don't include the id column in skeletons.
		unset($data['id']);
		$this->_actionController->view->result = 'ok';
		$this->_actionController->view->template = $data;
	}

	public function record(Celsus_Model $record) {
		$this->_actionController->view->result = 'ok';
		$this->_actionController->view->record = $record->toArray();
	}

	public function invalid(Celsus_Model $record) {
		$this->_fail("invalid", $record->getValidationMessages());
	}

	public function error(Celsus_Model $record, $message) {
		$this->_fail('error', $message . $record->toString());
	}

	protected function _fail($type, $detail) {
		$error = new StdClass;
		$error->type = $type;
		$error->detail = $detail;
		$this->_actionController->view->result = 'error';
		$this->_actionController->view->error = $error;

	}

	/**
	 * Returns data in a format suitable for using as a lookup on the client-side.
	 * Uses POST data to filter, if available.
	 */
	public function lookup() {
		$service = $this->_actionController->getService();
		$rawBody = $this->_actionController->getRequest()->getRawBody();
		$term = null;
		if ($rawBody) {
			$parameters = Zend_Json::decode($rawBody);
			if (array_key_exists('term', $parameters)) {
				$term = $parameters['term'];
			}
		}
		$this->_actionController->view->result = 'ok';
		$this->_actionController->view->data = $service::getLookupValues($term);
	}

}