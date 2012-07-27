<?php

class Celsus_Db_Document_Redis extends Celsus_DB_Document {

	/**
	 * Gets an atomic globally unique integer from Redis.
	 */
	protected function _generateId() {
		$this->_data['id'] = $this->_adapter->acquireId();
	}

	/**
	 * Inserts a document into the database and returns its id.
	 *
	 * For redis documents, we also generate a precise created timestamp
	 * which we can use to do sorted set range queries.
	 *
	 * @return mixed
	 */
	protected function _insert() {

		// PHP does weird things when casting floats to strings, so do it manually it here.
		// @see https://github.com/nicolasff/phpredis/issues/217
		$this->_data['_created'] = number_format(microtime(true), 8, '.', '');
		$this->_adapter->save($this);
		return $this->getId();
	}

}