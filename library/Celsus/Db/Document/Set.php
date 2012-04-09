<?php

class Celsus_Db_Document_Set implements Celsus_Db_Document_Set_Interface, Iterator, Countable, ArrayAccess {

	protected $_objectType = null;

	/**
	 * The documents in this set
	 *
	 * @var array
	 */
	protected $_documents = array();

	/**
	 * The adapter of this result set.
	 *
	 * @var Celsus_Db_Doc_Adapter_Couch
	 */
	protected $_adapter = null;

	public function __construct($config) {

		$this->_adapter = $config['adapter'];

		if (isset($config['data'])) {
			$data = $config['data'];
			if (is_string($data)) {
				$this->_loadFromJson($data);
			} elseif (is_array($data)) {
				$this->_loadFromArray($data);
			} else {
				throw new Celsus_Exception("Invalid data provided.");
			}
		}
	}

	protected function _loadFromJson($data) {
		return $this->_loadFromArray(Zend_Json::decode($data));
	}

	protected function _loadFromArray($data) {

		foreach ($data as $document) {
			$this->add($document);
		}
		return $this;
	}

	public function getAdapter() {
		return $this->_adapter;
	}

	/**
	 * Adds a document to the document set.
	 *
	 * @param array|Celsus_Db_Document_Simple $document
	 */
	public function add($document) {
		if (is_array($document)) {
			$document = new $this->_objectType(array(
				'adapter' => $this->getAdapter(),
				'data' => $document
			));
		} elseif (!$document instanceof Celsus_Db_Document_Simple) {
			throw new Celsus_Exception("Invalid document specified.");
		}

		$id = $document->getId();
		if (null == $id) {
			$this->_documents[] = $document;
		} else {
			$this->_documents[$id] = $document;
		}
	}

	public function toArray() {
		$return = array();
		foreach ($this->_documents as $document) {
			$return[] = $document->toArray();
		}
		return $return;
	}

	public function augment($fields) {
		foreach ($this->_documents as $document) {
			$document->augment($fields);
		}
	}

	public function count() {
		return count($this->_documents);
	}

	public function current() {
		return current($this->_documents);
	}

	public function key() {
		return key($this->_documents);
	}

	public function next() {
		return next($this->_documents);
	}

	public function rewind() {
		return reset($this->_documents);
	}

	public function valid() {
		return (false !== $this->current());
	}

	public function offsetSet($offset, $value) {
		$this->_documents[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->_documents[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->_documents[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->_documents[$offset]) ? $this->_documents[$offset] : null;
	}

}