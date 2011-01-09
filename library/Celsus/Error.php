<?php

class Celsus_Error {

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
			$request->setParam('error_handler', self::EXCEPTION_APPLICATION_ERROR);
		}
		throw new Celsus_Exception($message . " " . $file . " " . $line, $type);
	}

}