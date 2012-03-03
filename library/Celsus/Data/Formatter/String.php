<?php

class Celsus_Data_Formatter_String implements Celsus_Data_Formatter_Interface {

	/**
	 * Returns the data formatted as a string.
	 *
	 * @param Celsus_Data_Interface $object
	 * @return string
	 */
	public static function format(Celsus_Data_Interface $object) {
		return print_r($object->toArray(), true);
	}
}