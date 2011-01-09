<?php

class Celsus_Test_PHPUnit_ControllerTestCase_Cli extends Zend_Test_PHPUnit_ControllerTestCase {

	/**
	 * @var Zend_Application
	 */
	protected $_application;

	protected function _bootstrap() {
		$this->_application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/common.ini');
		$this->_application->setOptions(array(
			'config' => APPLICATION_PATH . '/configs/cli.ini'
		))->bootstrap();		
	}

	public function setUp() {
		$this->bootstrap = array(
			$this, 
			'_bootstrap'
		);

		parent::setUp();
	}

	/**
	 * Retrieve test case request object
	 *
	 * @return Celsus_Controller_Request_Cli
	 */
	public function getRequest() {
		if (null === $this->_request) {
			$this->_request = new Celsus_Controller_Request_CliTestCase();
		}
		return $this->_request;
	}

	/**
	 * Dispatch the MVC
	 *
	 * @param  string|null $url
	 * @return void
	 */
	public function dispatch($url = null) {
		// redirector should not exit
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		$redirector->setExit(false);
		
		// json helper should not exit
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		
		// task helper should not exit
		$taskExecutor = Zend_Controller_Action_HelperBroker::getStaticHelper('TaskExecutor');
		$taskExecutor->suppressExit(true);
		
		$request = $this->getRequest();
		if (null !== $url) {
			$request->setRequestUri($url);
		}
		$request->setPathInfo(null);
		$controller = $this->getFrontController();
		$this->frontController->setRequest($request)->setResponse($this->getResponse())->throwExceptions(false)->returnResponse(false);
		$this->frontController->dispatch();
	}
}
?>