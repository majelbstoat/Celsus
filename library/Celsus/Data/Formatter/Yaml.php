<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Data
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Yaml.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Formats a data object to YAML
 *
 * @category Celsus
 * @package Celsus_Data
 */
class Celsus_Data_Formatter_Yaml implements Celsus_Data_Formatter_Interface {

	/**
	 * Returns the data formatted as YAML.
	 *
	 * @param Celsus_Data_Interface $object
	 * @return string
	 */
	public static function format(Celsus_Data_Interface $object) {
		if (!function_exists('syck_dump')) {
			throw new Celsus_Exception("Syck is required for YAML output.");
		}
		return syck_dump($object->toArray());
	}
}