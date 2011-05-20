<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Auth
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Interface.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Interface also requiring a getResult() method.
 *
 * @category Celsus
 * @package Celsus_Auth
 */
interface Celsus_Auth_Adapter_Interface extends Zend_Auth_Adapter_Interface {

	/**
	 * Returns the result object.
	 *
	 * @return Celsus_Model
	 */
	public function getResult();
}