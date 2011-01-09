<?php

class Celsus_Db_Document_Couch {

	/**
	 * The document data
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * The adapter of this result set.
	 *
	 * @var Celsus_Db_Doc_Adapter_Couch
	 */
	protected $_adapter = null;

	public function __construct($config) {

		$this->_adapter = $config['adapter'];

		$data = $config['data'];
		if (is_string($data)) {
			$this->_loadFromJson($data);
		} elseif (is_array($data)) {
			$this->_loadFromArray($data);
		} else {
			throw new Celsus_Exception("Invalid data provided.");
		}
	}

	protected function _loadFromJson($data) {
		return $this->_loadFromArray(Zend_Json::decode($data));
	}

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

	/**
	 * Sets fields on the document.  Allows new fields to be added.
	 * @param array $data
	 * @return Celsus_Db_Document_Couch
	 */
	public function setFromArray(array $data) {
		foreach ($data as $key => $value) {
			$this->_data[$key] = $value;
		}
		return $this;
	}

	public function setId($id) {
		if (null === $id) {
			unset($this->_data['_id']);
		} else {
			$this->_data['id'] = $id;
		}
		return $this;
	}

	public function getId() {
		return isset($this->_data['_id']) ? $this->_data['_id'] : null;
	}

	public function getRevision() {
		return isset($this->_data['_rev']) ? $this->_data['_rev'] : null;
	}

	public function save() {
		if (!$this->getId()) {
			// Performance check.
			throw new Celsus_Exception("Not specifying an ID is a performance issue and is prohibited");
		}
		if ($this->getRevision()) {
			$this->_update();
		} else {
			$this->_insert();
		}

		// Ensure you update the rev id.
	}

	public function _update() {
		//$this->_preUpdate();

		$this->_data['_rev'] = $this->_adapter->save($this, Zend_Http_Client::PUT);

		//$this->_postUpdate();
	}

	public function _insert() {
		$this->_preInsert();

		$this->_data['_rev'] = $this->_adapter->save($this, Zend_Http_Client::POST);

		$this->_postInsert();
	}

	public function toArray() {
		return $this->_data;
	}

	public function toJson() {
		return Zend_Json::encode($this->_data);
	}
}