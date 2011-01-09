<?php

/**
 * Allows only fields beginning with d to be visible.
 * 
 * @author jamest
 *
 */
class Celsus_Data_Filter_DeesOnly implements Celsus_Data_Filter_Interface {
	
	/**
	 * Returns only fields that start with the letter D.
	 * 
	 * @param Celsus_Data_Interface $object
	 * @param array $fields
	 * @return array
	 */
	public static function filterReadable(Celsus_Data_Interface $object, $fields) {
		$return = array();
		foreach ($fields as $field) {
			if ('d' == strtolower(substr($field, 0, 1))) {
				$return[] = $field;
			} 
		}
		return $return;
	}
	
	/**
	 * Returns only fields that start with the letter D.
	 * 
	 * @param Celsus_Data_Interface $object
	 * @param array $fields
	 * @return array
	 */
	public static function filterWriteable(Celsus_Data_Interface $object, $fields) {
		return self::filterReadable($object, $fields);
	}
	
	
}