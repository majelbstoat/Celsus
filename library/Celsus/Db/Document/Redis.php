<?php

class Celsus_Db_Document_Redis extends Celsus_DB_Document {

	/**
	 * Gets an atomic globally unique integer from Redis.
	 */
	protected function _generateId() {
		$this->_data['id'] = $this->_adapter->acquireId();
	}

}