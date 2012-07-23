<?php

class Celsus_Db_Document_Redis_Query {

	const QUERY_TYPE_HASH_ELEMENT = 'hashElement';
	const QUERY_TYPE_SORTED_SET_RANGE = 'sortedSetRange';
	const QUERY_TYPE_SORTED_SET_SCORE = 'sortedSetScore';

	protected $_indexType = null;

	protected $_parameters = null;

	public function __construct($options = array()) {
		$this->_indexType = $options['indexType'];
		$this->_parameters = $options['parameters'];
	}

	public function getIndexType() {
		 return $this->_indexType;
	}

	public function getParameters() {
		return $this->_parameters;
	}
}
