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

	/**
	 * Handles notices, warnings and errors in the application and converts them to
	 * exceptions which will be handling by the main dispatch loop.
	 *
	 * When paired with the registered shutdown function, this even correctly handles
	 * fatal errors and even parse errors, so users aren't left with blank screen.
	 *
	 * @param int $type
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 */
	public static function handle($type, $message, $file, $line, $fatal = false) {

		$serviceManager = Celsus_Service_Manager::getInstance();
		$state = $serviceManager->getState();

		// Set the exception.
		$exception = new Celsus_Exception($message, Celsus_Http::INTERNAL_SERVER_ERROR);
		$exception->setFile($file)->setLine($line);

		$state->setException($exception);
		self::_reprocess();
	}

	/**
	 * Handles uncaught exceptions and re-processes the request to ensure the error is
	 * communicated to the user.
	 *
	 * @param Exception $exception
	 */
	public static function exception(Exception $exception) {
		$serviceManager = Celsus_Service_Manager::getInstance();
		$state = $serviceManager->getState();

		$state->setException($exception);
		self::_reprocess();
	}

	/**
	 * Handles fatal, compile and parse errors, passing through to the error handler,
	 * and re-dispatching the request.
	 */
	public static function shutdown() {
		$error = error_get_last();
		if (null !== $error) {
			// Turn the error into an exception, which can be set on the application state.
			self::handle($error['type'], $error['message'], $error['file'], $error['line'], true);
		}
	}

	/**
	 * Reprocesses a request following uncaught exceptions or fatal errors.
	 */
	protected static function _reprocess() {
		// The exception handler and request loop will have been destroyed, so we need to reprocess.
		$manager = Celsus_Service_Manager::getInstance();
		$state = $manager->getState();

		$manager->getResponseManager()->determineContext($state);
		$state->getRequest()->setDispatched(false);
		$manager->process();
	}

}
