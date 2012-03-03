<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Error.php 72M 2010-09-20 21:32:08Z (local) $
 */

/**
 * Error handling functionality
 *
 * @defgroup Celsus_Error Celsus Error
 */

/**
 * Provides error triggering and handling.
 *
 * @ingroup Celsus_Error
 */
class Celsus_Error {

	const ERROR_FLAG = 'error_handler';

	const EXCEPTION_APPLICATION_ERROR = 'EXCEPTION_APPLICATION_ERROR';

	const EXCEPTION_NOT_FOUND = 'EXCEPTION_NOT_FOUND';

	/**
	 * Handles notices, warnings and errors and turns them into exceptions
	 * which can be handled by the exception handling mechanism.
	 *
	 * @todo Do some logging here?
	 * @param int $type
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 */
	public static function handle($type, $message, $file, $line) {

		$request = Zend_Controller_Front::getInstance()->getRequest();
		if ($request) {
			// Sets the parameter on the request.
			$request->setParam(Celsus_Error::ERROR_FLAG, self::EXCEPTION_APPLICATION_ERROR);
		}
		throw new Celsus_Exception("$message\n in $file on line $line", $type);
	}

}