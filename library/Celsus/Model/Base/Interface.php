<?php

interface Celsus_Model_Base_Interface {

	/**
	 * Finds records based on identifier.
	 * @return Celsus_Data_Collection
	 */
	public function find();


	/**
	 * Finds multiple records based on the supplied parameters.
	 * @return Celsus_Data_Collection
	 *
	 */
	public function fetchAll();

	/**
	 * Deletes from permanent storage, based on the supplied query.
	 * @param array|string $where
	 */
	public function delete($where);

	/**
	 * Creates a new record, filled with default data, ready to be populated.
	 * @return Celsus_Data_Object
	 */
	public function createRecord(array $data = array());
}