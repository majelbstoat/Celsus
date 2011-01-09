<?php

interface Celsus_Data_Marshal_Interface {

	/**
	 * Provides the data from the underyling source for a CDO.
	 */
	public static function provide($object);

	/**
	 * Returns the class that this marshals.
	 */
	public static function provides();

	/**
	 * Handles the saving of this data back to the underlying layer.
	 */
	public static function save(array $data, $object);
}

?>