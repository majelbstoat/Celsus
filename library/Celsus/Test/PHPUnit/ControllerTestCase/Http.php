<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Test
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Http.php 69 2010-09-08 12:32:03Z jamie $
 */

/** @see PHPUnit_Runner_Version */
require_once 'PHPUnit/Runner/Version.php';

/**
 * Depending on version, include the proper PHPUnit support
 * @see PHPUnit_Autoload
 */
require_once (version_compare(PHPUnit_Runner_Version::id(), '3.5.0', '>=')) ? 'PHPUnit/Autoload.php' : 'PHPUnit/Framework.php';

require_once 'Celsus/Test/PHPUnit/ControllerTestCase.php';

/**
 * Controller test harness that boots an application with the specified components.
 *
 * @category Celsus
 * @package Celsus_Test
 */
abstract class Celsus_Test_PHPUnit_ControllerTestCase_Http extends Celsus_Test_PHPUnit_ControllerTestCase {

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
	 * Reset MVC state
	 *
	 * Creates new request/response objects, resets the front controller
	 * instance, and resets the action helper broker.
	 *
	 * Also clears any authentication session.
	 *
	 * @return void
	 */
	public function reset() {
		require_once 'Zend/Registry.php';
		Zend_Registry::_unsetInstance();

		require_once 'Celsus/Auth.php';
		Celsus_Auth::getInstance()->clearIdentity();
		Celsus_Auth::resetAuthAdapter();

		require_once 'Celsus/Db.php';
		Celsus_Db::flushDatabases();
		Celsus_Db::resetAdapters();

		require_once 'Celsus/Model/Base.php';
		Celsus_Model_Base::resetAdapters();

		$_SESSION = array();
		$_COOKIE  = array();

		// Undo the mocking set up by the test.
		$this->_mock()->reset();

		// Reset the request and response objects.
		$this->resetRequest()->resetResponse();
		Zend_Session::$_unitTestEnabled = true;
	}

	protected function _bootstrap() {
		$application = APPLICATION_CLASS;

		// Require manually, as this will be executed before autoloading.
		require_once APPLICATION_CLASS . ".php";

		$configuration =
		$this->_application = new $application (APPLICATION_ENV, array(
			APPLICATION_PATH . '/configs/common.yaml',
			APPLICATION_PATH . '/configs/web.yaml'
		), false);
		$this->_application->bootstrap($this->_bootstrapComponents, $this->_excludedBootstrapComponents);
	}

	/**
	 * Performs exactly the same job as the parent class, but specifically enables the throwing of exceptions.
	 *
	 * @param  string|null $url
	 * @return void
	 */
	public function dispatch($url = null) {

		$request = $this->getRequest();
		if (null !== $url) {
			$request->setRequestUri($url);
		}
		$request->setPathInfo(null);

		$this->_serviceManager->getState()
			->setRequest($request)
			->setResponse($this->getResponse());

		$this->_serviceManager->getResponseManager()->returnResponse(true);

		$this->_serviceManager->handle();
	}

	/**
	 * Provides a mechanism by which parts of the SUT can be mocked and stubbed out.
	 *
	 * @param boolean $force Certain components should always be mocked, even during integration testing.
	 */
	protected function _mock($force = false) {
		if (null === $this->_mockBroker) {
			require_once 'Celsus/Test/Mock/Broker.php';
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
			$this->_request = new Celsus_Controller_Request_HttpTestCase();
		}
		return $this->_request;
	}

	/**
	 * Retrieve test case response object
	 *
	 * @return Zend_Controller_Response_HttpTestCase
	 */
	public function getResponse()
	{
		if (null === $this->_response) {
			require_once 'Celsus/Controller/Response/HttpTestCase.php';
			$this->_response = new Celsus_Controller_Response_HttpTestCase;
		}
		return $this->_response;
	}


	/**
	 * Reset the request object
	 *
	 * Useful for test cases that need to test multiple trips to the server.
	 *
	 * @return Zend_Test_PHPUnit_ControllerTestCase
	 */
	public function resetRequest()
	{
		require_once 'Celsus/Controller/Request/HttpTestCase.php';
		if ($this->_request instanceof Celsus_Controller_Request_HttpTestCase) {
			$this->_request->clearQuery()->clearPost();
		}
		$this->_request = null;
		return $this;
	}

	/**
	 * Reset the response object
	 *
	 * Useful for test cases that need to test multiple trips to the server.
	 *
	 * @return Zend_Test_PHPUnit_ControllerTestCase
	 */
	public function resetResponse()
	{
		$this->_response = null;
		return $this;
	}
}
