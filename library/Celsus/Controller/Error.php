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
abstract class Celsus_Controller_Error extends Zend_Controller_Action {

	public function init() {
		if (null === $this->_getParam('error_handler')) {
			// No error parameters means that the controller has been accessed directly, which is disallowed.
			return $this->_redirect("/");
		}
		parent::init();

	}

	public function errorAction() {
		$this->_helper->layout->setLayout('error');
		$error = $this->_getParam('error_handler');
		if (null === $error) {
			return;
		}

		$exceptions = array();
		foreach ($this->getResponse()->getException() as $exception) {
			$exceptions[] = $exception->getMessage() . " " . $exception->getFile() . " " . $exception->getLine();
		}
		$this->view->exceptions = $exceptions;

		$errorType = ($error->type) ? $error->type : $error;

		if (Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER == $errorType) {
			// This was an error triggered during the application.
			$errorType = $error->exception->getCode();
		}

		switch ($errorType) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
			case Celsus_Http::NOT_FOUND:

				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(Celsus_Http::NOT_FOUND);
				$this->_helper->viewRenderer->setScriptAction(Celsus_Http::NOT_FOUND);
				$this->view->message = 'Page Not Found';
				break;

			case Celsus_Error::EXCEPTION_APPLICATION_ERROR:
			default:
				$this->getResponse()->setHttpResponseCode(Celsus_Http::INTERNAL_SERVER_ERROR);
				$this->_helper->viewRenderer->setScriptAction(Celsus_Http::INTERNAL_SERVER_ERROR);
				$this->view->message = 'Application Error';
				break;
		}
	}

}
?>