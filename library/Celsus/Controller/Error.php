<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Error.php 72 2010-09-14 01:56:33Z jamie $
 */

/**
 * Default error controller for when something goes wrong.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
abstract class Celsus_Controller_Error extends Celsus_Controller_Common {

	public function errorAction() {

		$exception = $this->_state->getException();
		$responseModel = $this->getResponseModel();

		if (null === $exception) {
			// No exception means that the controller has been accessed directly, which is disallowed.
			return $responseModel->setResponseType('noError');
		}

		$errorType = $exception->getCode();

		// @todo Only add exception data to the response model if the user is an admin.
		$errorData = array(
			'code' => $errorType,
			'exception' => $exception
		);

		switch ($errorType) {
			case Celsus_Http::NOT_FOUND:
				// 404 error -- controller or action not found
				$responseModel->setResponseType('notFound');
				$errorData['headline'] = 'Page Not Found';
				break;

			case Celsus_Http::METHOD_NOT_ALLOWED:
				// 405 error -- Invalid HTTP method specified for the endpoint.
				$responseModel->setResponseType('methodNotAllowed');
				$errorData['headline'] = 'Method Not Allowed';
				$errorData['method'] = $this->getRequest()->getMethod();
				break;

			default:
				// An unspecified error
				$responseModel->setResponseType('error');
				$errorData['headline'] = 'Application Error';
				$errorData['code'] = Celsus_Http::INTERNAL_SERVER_ERROR;
				break;
		}

		// Set the error data on the response model.
		$responseModel->setData($errorData);

		// We want to set the response code, regardless of the context.
		$this->_state->getResponse()->setHttpResponseCode($errorData['code']);
	}

}
?>