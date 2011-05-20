<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Model
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

/**
 * Exception to be thrown when models aren't found.
 *
 * @category Celsus
 * @package Celsus_Model
 */
class Celsus_Model_Exception_NotFound extends Exception {

	public function __construct($message, $code = null, $previous = null) {
		parent::__construct($message, Celsus_Http::NOT_FOUND, $previous);
	}
}