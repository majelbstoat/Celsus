<?php

/**
 * Describes an interface for filtering
 */
interface Celsus_Data_Filter_Interface {
	
	/**
	 * Determines which fields are readable for the supplied object, based on
	 * current security model.
	 * 
	 * @param Celsus_Data_Interface $object
	 * @param array $fields
	 * @return array
	 */
	public static function filterReadable(Celsus_Data_Interface $object, $fields);

	/**
	 * Determines which fields are writeable for the supplied object, based on
	 * current security model.
	 * 
	 * @param Celsus_Data_Interface $object
	 * @param array $fields
	 * @return array
	 */
	public static function filterWriteable(Celsus_Data_Interface $object, $fields);
	
}


?>