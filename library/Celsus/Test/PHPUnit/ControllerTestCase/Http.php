<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Test
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Http.php 69 2010-09-08 12:32:03Z jamie $
 */

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

/**
 * Controller test harness that boots an application with the specified components.
 *
 * @category Celsus
 * @package Celsus_Test
 */
class Celsus_Test_PHPUnit_ControllerTestCase_Http extends Zend_Test_PHPUnit_ControllerTestCase {

	/**
	 * The application instance to test with.
	 *
	 * @var Celsus_Application
	 */
	protected $_application;

	/**
	 * Defines the components of the bootstrap that are needed for this test.
	 *
	 * @var array
	 */
	protected $_bootstrapComponents = null;

	/**
	 * Defines application components not needed for these tests.  Ignored if
	 * $this->_bootstrapComponents is set.
	 *
	 * @var array
	 */
	protected $_excludedBootstrapComponents = array();

	/**
	 * Broker that provides mocking capabilities.
	 *
	 * @var Celsus_Test_Mock_Broker
	 */
	protected $_mockBroker = null;

	/**
	 * Resets the state after every test.
	 */
	protected function tearDown() {
		$this->reset();
	}

	/**
	 * Also clears any authentication session.
	 */
	public function reset() {
		require_once 'Zend/Auth.php';
		require_once 'Celsus/Auth.php';
		Zend_Registry::_unsetInstance();
		Zend_Auth::getInstance()->clearIdentity();
		return parent::reset();
	}

	protected function _bootstrap() {
		$application = APPLICATION_CLASS;

		// Require manually, as this will be executed before autoloading.
		require_once APPLICATION_CLASS . ".php";

		$this->_application = new $application (APPLICATION_ENV, array(
			APPLICATION_PATH . '/configs/common.yaml',
			APPLICATION_PATH . '/configs/web.yaml'
		), false);
		$this->_application->bootstrap($this->_bootstrapComponents, $this->_excludedBootstrapComponents);
	}

	public function setUp() {
		$this->bootstrap = array($this, '_bootstrap');
		parent::setUp();
	}

	/**
	 * Performs exactly the same job as the parent class, but specifically enables the throwing of exceptions.
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

		$request = $this->getRequest();
		if (null !== $url) {
			$request->setRequestUri($url);
		}
		$request->setPathInfo(null);

		$controller = $this->getFrontController();
		$this->frontController
			->setRequest($request)
			->setResponse($this->getResponse())
			->throwExceptions(true)
			->returnResponse(false);

		if ($this->bootstrap instanceof Zend_Application) {
			$this->bootstrap->run();
		} else {
			$this->frontController->dispatch();
		}
	}

	/**
	 * Provides a mechanism by which parts of the SUT can be mocked and stubbed out.
	 *
	 * @param boolean $force Certain components should always be mocked, even during integration testing.
	 */
	protected function _mock($force = false) {
		if (null === $this->_mockBroker) {
			$this->_mockBroker = new Celsus_Test_Mock_Broker(APPLICATION_CLASS . '_Mock_');
		}

		// We enable mocking if we are not integration testing, or if explicitly forced.
		$this->_mockBroker->setEnabled($force || !INTEGRATION_TESTING);
		return $this->_mockBroker;
	}

	public function getMockObject() {
		return call_user_func_array(array($this, 'getMock'), func_get_args());
	}

	/**
	 * Retrieve test case request object
	 *
	 * @return Celsus_Controller_Request_HttpTestCase
	 */
	public function getRequest()
	{
		if (null === $this->_request) {
			$this->_request = new Celsus_Controller_Request_HttpTestCase;
		}
		return $this->_request;
	}

	// Additional Assertions

	/**
	 * Asserts that we are in the expect context (json, xml etc).
	 *
	 * @param string $context
	 */
	public function assertContext($context) {
		$this->_incrementAssertionCount();
		$actualContext = Zend_Controller_Action_HelperBroker::getStaticHelper('ContextSwitch')->getCurrentContext();
		if ($context != $actualContext) {
			$message = sprintf('Failed asserting context <"%s"> was "%s"', $actualContext, $context);
			$this->fail($message);
		}
	}

	public function assertFeedback($code) {
		$this->_incrementAssertionCount();
		if (!Celsus_Feedback::has($code)) {
			$message = sprintf('Failed asserting feedback of <"%s">', $code);
			$this->fail($message);
		}
	}
}
