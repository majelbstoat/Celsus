<?php

interface Celsus_Model_Service_Interface {

	/**
	 * Finds a single record by unique identifier.
	 *
	 * @param mixed $identifier
	 */
	public static function find($identifier);

	/**
	 * Finds multiple records based on the supplied parameters.
	 */
	public static function fetchAll();

	/**
	 * Given an $identifier, returns the record that matches, or else returns a blank record.
	 * @param mixed $identifier
	 */
	public static function fetchOrCreateRecord($identifier);

}