<?php

class Celsus_Db_Document_Couch extends Celsus_Db_Document {

	/**
	 * Initially populates the data, and handles include_docs.
	 * @param array $data
	 */
	protected function _loadFromArray(array $data) {
		if (array_key_exists('doc', $data)) {
			$data = $data['doc'];
		}
		$this->_data = $data;
	}

	public function setId($id) {
		if (null === $id) {
			unset($this->_data['_id']);
		} else {
			$this->_data['_id'] = $id;
		}
		return $this;
	}

	public function getId() {
		return isset($this->_data['_id']) ? $this->_data['_id'] : null;
	}

	public function getRevision() {
		return isset($this->_data['_rev']) ? $this->_data['_rev'] : null;
	}

	/**
	 * Updates a document in the database and returns its id.
	 */
	public function _update() {
		$this->_data['_rev'] = $this->_adapter->save($this, Zend_Http_Client::PUT);
		return $this->getId();
	}

	/**
	 * Inserts a document into the database and returns its id.
	 *
	 * @return mixed
	 */
	public function _insert() {
		// Unset the revision for new documents, just to be sure.
		unset($this->_data['_rev']);

		$this->_data['_rev'] = $this->_adapter->save($this, Zend_Http_Client::PUT);
		return $this->getId();
	}
}