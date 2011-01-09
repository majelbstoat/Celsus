<?php

class Celsus_Data_Formatter_Json implements Celsus_Data_Formatter_Interface {
	
	/**
	 * Returns the data formatted as Json.
	 * 
	 * @param Celsus_Data_Interface $object
	 * @return string
	 */
	public static function format(Celsus_Data_Interface $object) {
		return Zend_Json::encode($object->toArray());
	}
}