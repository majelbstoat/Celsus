<?php

interface Celsus_Model_Base_Interface {

	/**
	 * Finds records based on identifier.
	 * @return mixed
	 */
	public function find();


	/**
	 * Finds multiple records based on the supplied parameters.
	 * @return mixed
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
	 * @return mixed
	 */
	public function createRecord(array $data = array());

	/**
	 * Returns a list of the fields in this base class.
	 * @return array
	 */
	public function getFields();
}