<?php

class Celsus_Db_Document {

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
		$this->_data = $data;
	}

	/**
	 * Sets fields on the document.  Allows new fields to be added.
	 *
	 * @param array $data
	 * @return Celsus_Db_Document
	 */
	public function setFromArray(array $data) {
		foreach ($data as $key => $value) {
			$this->_data[$key] = $value;
		}
		return $this;
	}

	public function setId($id) {
		if (null === $id) {
			unset($this->_data['id']);
		} else {
			$this->_data['id'] = $id;
		}
		return $this;
	}

	public function getId() {
		return isset($this->_data['id']) ? $this->_data['id'] : null;
	}

	/**
	 * When an ID is not specified, generates a base-64 shortened id derived from
	 * the number of microseconds since the epoch, plus two random base-64 digits.
	 *
	 * @return string
	 */
	protected function _generateId() {
		list($microseconds, $seconds) = explode(" ", microtime());
		$now = $seconds . substr($microseconds, 2, 6);
		$this->setId(Celsus_Encoder::encode($now) . Celsus_Encoder::encode(rand(0, 4095)));
	}

	public function save() {
		if (!$this->getId()) {
			// Performance check.
			$new = true;
			$this->_generateId();
		} else {
			$new = false;
		}

		// If a field is empty, remove the field.
		// The application layer will take care of exposing the non-existence to the outside world.
		$fields = array_keys($this->_data);
		foreach ($fields as $field) {
			if (null === $this->_data[$field]) {
				unset($this->_data[$field]);
			}
		}

		return ($new) ? $this->_insert() : $this->_update();
	}

	/**
	 * Ensures that this document contains items for the supplied fields (which will be null if previously missing).
	 *
	 * @param unknown_type $fields
	 */
	public function augment($fields) {
		$this->_data = array_merge(array_combine($fields, array_fill(0, count($fields), null)), $this->_data);
	}

	/**
	 * Updates a document in the database and returns its id.
	 */
	protected function _update() {
		$this->_adapter->save($this);
		return $this->getId();
	}

	/**
	 * Inserts a document into the database and returns its id.
	 *
	 * @return mixed
	 */
	protected function _insert() {
		$this->_adapter->save($this);
		return $this->getId();
	}

	public function toArray() {
		return $this->_data;
	}

	public function toJson() {
		return Zend_Json::encode($this->_data);
	}

	public function __get($index) {
		return $this->_data[$index];
	}
}