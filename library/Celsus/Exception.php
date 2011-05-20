<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Exception.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Exception handling functionality
 *
 * @defgroup Celsus_Exception Celsus Exception
 */

/**
 * Defines a standard exception and methods for handling them,
 * based on the the environment.
 *
 * @ingroup Celsus_Auth
 */
class Celsus_Exception extends Exception {

	public static function production_handler($exception) {
		$logger = Zend_Registry::get('logger');
		$logger->log("Unhandled exception: " . $exception->getMessage(). "\n" . $exception->getTraceAsString(), Zend_Log::ALERT);
	}

}

?>