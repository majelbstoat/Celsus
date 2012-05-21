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

	public function init() {
		if (null === $this->getRequest()->getError()) {
			// No error parameters means that the controller has been accessed directly, which is disallowed.
			return $this->_redirect(Celsus_Routing::absoluteLinkTo('home'));
		}

//		$this->_helper->layout->setLayout('error');

		parent::init();
	}

	public function errorAction() {

		$error = $this->getRequest()->getError();

		$errorDetails = new stdClass();
		switch ($errorType) {
			case Celsus_Http::NOT_FOUND:

				// 404 error -- controller or action not found
				$errorCode = Celsus_Http::NOT_FOUND;
				$errorDetails->title = 'Page Not Found';
				break;

			case Celsus_Http::METHOD_NOT_ALLOWED:

				// 405 error -- Invalid HTTP method specified for the endpoint.
				$errorCode = Celsus_Http::METHOD_NOT_ALLOWED;
				$errorDetails->title = 'Method Not Allowed';
				$errorDetails->method = $this->getRequest()->getMethod();
				break;

			default:

				// An unspecified error
				$errorCode = Celsus_Http::INTERNAL_SERVER_ERROR;
				$errorDetails->title = 'Application Error';
				break;
		}

		$this->getResponse()->setHttpResponseCode($errorCode);
//		$this->_helper->viewRenderer->setScriptAction($errorCode);
		$error->type = $errorCode;
		$error->details = $errorDetails;

//		$this->view->error = $error;
	}

}
?>