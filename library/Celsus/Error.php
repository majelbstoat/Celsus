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
	 * Handles notices, warnings and errors in the application and dipatches the
	 * error controller to render them appropriately.
	 *
	 * When paired with the registered shutdown function, this even correctly handles
	 * fatal errors and even parse errors, so users aren't left with blank screen.
	 *
	 * @param int $type
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 */
	public static function handle($type, $message, $file, $line) {

		$request = Zend_Controller_Front::getInstance()->getRequest();

		// Sets the parameter on the request.
		$error = new stdClass();
		$error->type = Celsus_Error::EXCEPTION_APPLICATION_ERROR;

		// Set the error on the request.
		$request->setParams(array(
			Celsus_Error::ERROR_FLAG => $error
		));

		$exception = new Celsus_Exception($message, $type);
		$exception->setFile($file)->setLine($line);

		$response = Zend_Controller_Front::getInstance()->getResponse();
		$response->setException($exception);

		// Ensure the error controller is going to be routed.
		$request->setDispatched(false)
			->setRequestUri('error/error')
			->setPathInfo();

		// Clear whatever has been rendered so far.
		ob_get_clean();

//		Celsus_Log::error((string) $exception);

		// Dispatch the error request.
		Zend_Controller_Front::getInstance()->dispatch($request, $response);

		// Dying is technically not the smartest thing to do, but it prevents multiple repeat dispatches
		// in strange cases where two errors appear on the same line like:  $nonExistant::BAD_CONSTANT
		die;
	}

	/**
	 * Handles fatal, compile and parse errors, passing through to the error handler.
	 */
	public static function shutdown() {
		$error = error_get_last();
		if (null !== $error) {
			// The exception handler and request loop will have been destroyed, so we need to handle appropriately.
			self::handle($error['type'], $error['message'], $error['file'], $error['line']);
		}
	}

}