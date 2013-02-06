<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Data
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Interface.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Defines an interface to format data objects
 *
 * @category Celsus
 * @package Celsus_Data
 */
interface Celsus_Data_Formatter_Interface {

	public static function format(Celsus_Data_Interface $object);

}