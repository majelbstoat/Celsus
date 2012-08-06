<?php

class Celsus_Db_Mock_Adapter_Redis extends Celsus_Db_Document_Adapter_Redis {

	/**
	 * Gets the client used to execute commands.
	 *
	 * @return Redis
	 */
	public function getClient() {
		if (null === $this->_client) {
			$this->_client = new Celsus_Db_Mock_Adapter_Redis_Server();
		}
		return $this->_client;
	}
}