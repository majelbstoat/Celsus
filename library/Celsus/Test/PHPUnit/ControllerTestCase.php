<?php

abstract class Celsus_Test_PHPUnit_ControllerTestCase extends PHPUnit_Framework_TestCase {

	/**
	 * @var Zend_Dom_Query
	 */
	protected $_query;

	/**
	 * @var Zend_Controller_Request_Abstract $_request
	 */
	protected $_request;

	/**
	 * The response object.
	 *
	 * @var Zend_Controller_Response_Abstract $_response
	 */
	protected $_response;

	/**
	 * The service manager.
	 *
	 * @var Celsus_Service_Manager $_serviceManager
	 */
	protected $_serviceManager = null;

	/**
	 * The namespaces registered for XPath.
	 *
	 * @var array $_xpathNamespaces
	 */
	protected $_xpathNamespaces = array();

	protected function setUp() {
		require_once 'Celsus/Service/Manager.php';
		$this->_serviceManager = Celsus_Service_Manager::getInstance();
		$this->bootstrap();
	}

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

	/**
	 * Assert against DOM selection
	 *
	 * @param  string $path CSS selector path
	 * @param  string $message
	 * @return void
	 */
	public function assertQuery($path, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against DOM selection
	 *
	 * @param  string $path CSS selector path
	 * @param  string $message
	 * @return void
	 */
	public function assertNotQuery($path, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against DOM selection; node should contain content
	 *
	 * @param  string $path CSS selector path
	 * @param  string $match content that should be contained in matched nodes
	 * @param  string $message
	 * @return void
	 */
	public function assertQueryContentContains($path, $match, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against DOM selection; node should NOT contain content
	 *
	 * @param  string $path CSS selector path
	 * @param  string $match content that should NOT be contained in matched nodes
	 * @param  string $message
	 * @return void
	 */
	public function assertNotQueryContentContains($path, $match, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against DOM selection; node should match content
	 *
	 * @param  string $path CSS selector path
	 * @param  string $pattern Pattern that should be contained in matched nodes
	 * @param  string $message
	 * @return void
	 */
	public function assertQueryContentRegex($path, $pattern, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against DOM selection; node should NOT match content
	 *
	 * @param  string $path CSS selector path
	 * @param  string $pattern pattern that should NOT be contained in matched nodes
	 * @param  string $message
	 * @return void
	 */
	public function assertNotQueryContentRegex($path, $pattern, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against DOM selection; should contain exact number of nodes
	 *
	 * @param  string $path CSS selector path
	 * @param  string $count Number of nodes that should match
	 * @param  string $message
	 * @return void
	 */
	public function assertQueryCount($path, $count, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against DOM selection; should NOT contain exact number of nodes
	 *
	 * @param  string $path CSS selector path
	 * @param  string $count Number of nodes that should NOT match
	 * @param  string $message
	 * @return void
	 */
	public function assertNotQueryCount($path, $count, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against DOM selection; should contain at least this number of nodes
	 *
	 * @param  string $path CSS selector path
	 * @param  string $count Minimum number of nodes that should match
	 * @param  string $message
	 * @return void
	 */
	public function assertQueryCountMin($path, $count, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against DOM selection; should contain no more than this number of nodes
	 *
	 * @param  string $path CSS selector path
	 * @param  string $count Maximum number of nodes that should match
	 * @param  string $message
	 * @return void
	 */
	public function assertQueryCountMax($path, $count, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Register XPath namespaces
	 *
	 * @param   array $xpathNamespaces
	 * @return  void
	 */
	public function registerXpathNamespaces($xpathNamespaces)
	{
		$this->_xpathNamespaces = $xpathNamespaces;
	}

	/**
	 * Assert against XPath selection
	 *
	 * @param  string $path XPath path
	 * @param  string $message
	 * @return void
	 */
	public function assertXpath($path, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content = $this->_response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against XPath selection
	 *
	 * @param  string $path XPath path
	 * @param  string $message
	 * @return void
	 */
	public function assertNotXpath($path, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against XPath selection; node should contain content
	 *
	 * @param  string $path XPath path
	 * @param  string $match content that should be contained in matched nodes
	 * @param  string $message
	 * @return void
	 */
	public function assertXpathContentContains($path, $match, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against XPath selection; node should NOT contain content
	 *
	 * @param  string $path XPath path
	 * @param  string $match content that should NOT be contained in matched nodes
	 * @param  string $message
	 * @return void
	 */
	public function assertNotXpathContentContains($path, $match, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against XPath selection; node should match content
	 *
	 * @param  string $path XPath path
	 * @param  string $pattern Pattern that should be contained in matched nodes
	 * @param  string $message
	 * @return void
	 */
	public function assertXpathContentRegex($path, $pattern, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against XPath selection; node should NOT match content
	 *
	 * @param  string $path XPath path
	 * @param  string $pattern pattern that should NOT be contained in matched nodes
	 * @param  string $message
	 * @return void
	 */
	public function assertNotXpathContentRegex($path, $pattern, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against XPath selection; should contain exact number of nodes
	 *
	 * @param  string $path XPath path
	 * @param  string $count Number of nodes that should match
	 * @param  string $message
	 * @return void
	 */
	public function assertXpathCount($path, $count, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against XPath selection; should NOT contain exact number of nodes
	 *
	 * @param  string $path XPath path
	 * @param  string $count Number of nodes that should NOT match
	 * @param  string $message
	 * @return void
	 */
	public function assertNotXpathCount($path, $count, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against XPath selection; should contain at least this number of nodes
	 *
	 * @param  string $path XPath path
	 * @param  string $count Minimum number of nodes that should match
	 * @param  string $message
	 * @return void
	 */
	public function assertXpathCountMin($path, $count, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content    = $this->response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert against XPath selection; should contain no more than this number of nodes
	 *
	 * @param  string $path XPath path
	 * @param  string $count Maximum number of nodes that should match
	 * @param  string $message
	 * @return void
	 */
	public function assertXpathCountMax($path, $count, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		$constraint->registerXpathNamespaces($this->_xpathNamespaces);
		$content    = $this->_response->outputBody();
		if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
			$constraint->fail($path, $message);
		}
	}

	/**
	 * Assert that response is a redirect
	 *
	 * @param  string $message
	 * @return void
	 */
	public function assertRedirect($message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
		if (!$constraint->evaluate($this->_response, __FUNCTION__)) {
			$constraint->fail($this->_response, $message);
		}
	}

	/**
	 * Assert that response is NOT a redirect
	 *
	 * @param  string $message
	 * @return void
	 */
	public function assertNotRedirect($message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
		if (!$constraint->evaluate($this->_response, __FUNCTION__)) {
			$constraint->fail($this->_response, $message);
		}
	}

	/**
	 * Assert that response redirects to given URL
	 *
	 * @param  string $url
	 * @param  string $message
	 * @return void
	 */
	public function assertRedirectTo($url, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
		if (!$constraint->evaluate($this->_response, __FUNCTION__, $url)) {
			$constraint->fail($this->_response, $message);
		}
	}

	/**
	 * Assert that response does not redirect to given URL
	 *
	 * @param  string $url
	 * @param  string $message
	 * @return void
	 */
	public function assertNotRedirectTo($url, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $url)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert that redirect location matches pattern
	 *
	 * @param  string $pattern
	 * @param  string $message
	 * @return void
	 */
	public function assertRedirectRegex($pattern, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $pattern)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert that redirect location does not match pattern
	 *
	 * @param  string $pattern
	 * @param  string $message
	 * @return void
	 */
	public function assertNotRedirectRegex($pattern, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $pattern)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert response code
	 *
	 * @param  int $code
	 * @param  string $message
	 * @return void
	 */
	public function assertResponseCode($code, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
		if (!$constraint->evaluate($this->_response, __FUNCTION__, $code)) {
			$constraint->fail($this->_response, $message);
		}
	}

	/**
	 * Assert response code
	 *
	 * @param  int $code
	 * @param  string $message
	 * @return void
	 */
	public function assertNotResponseCode($code, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
		$constraint->setNegate(true);
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $code)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert response header exists
	 *
	 * @param  string $header
	 * @param  string $message
	 * @return void
	 */
	public function assertHeader($header, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $header)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert response header does not exist
	 *
	 * @param  string $header
	 * @param  string $message
	 * @return void
	 */
	public function assertNotHeader($header, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
		$constraint->setNegate(true);
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $header)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert response header exists and contains the given string
	 *
	 * @param  string $header
	 * @param  string $match
	 * @param  string $message
	 * @return void
	 */
	public function assertHeaderContains($header, $match, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $header, $match)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert response header does not exist and/or does not contain the given string
	 *
	 * @param  string $header
	 * @param  string $match
	 * @param  string $message
	 * @return void
	 */
	public function assertNotHeaderContains($header, $match, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
		$constraint->setNegate(true);
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $header, $match)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert response header exists and matches the given pattern
	 *
	 * @param  string $header
	 * @param  string $pattern
	 * @param  string $message
	 * @return void
	 */
	public function assertHeaderRegex($header, $pattern, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $header, $pattern)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert response header does not exist and/or does not match the given regex
	 *
	 * @param  string $header
	 * @param  string $pattern
	 * @param  string $message
	 * @return void
	 */
	public function assertNotHeaderRegex($header, $pattern, $message = '')
	{
		$this->_incrementAssertionCount();
		require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
		$constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
		$constraint->setNegate(true);
		$response   = $this->response;
		if (!$constraint->evaluate($response, __FUNCTION__, $header, $pattern)) {
			$constraint->fail($response, $message);
		}
	}

	/**
	 * Assert that the last handled request used the given controller
	 *
	 * @param  string $controller
	 * @param  string $message
	 * @return void
	 */
	public function assertController($controller, $message = '')
	{
		$this->_incrementAssertionCount();
		$selectedController = $this->_serviceManager->getState()->getRoute()->getController();
		if ($controller != $selectedController) {
			$msg = sprintf('Failed asserting last controller used <"%s"> was "%s"',
				$selectedController,
				$controller
			);
			if (!empty($message)) {
				$msg = $message . "\n" . $msg;
			}
			$this->fail($msg);
		}
	}

	/**
	 * Assert that the last handled request did NOT use the given controller
	 *
	 * @param  string $controller
	 * @param  string $message
	 * @return void
	 */
	public function assertNotController($controller, $message = '')
	{
		$this->_incrementAssertionCount();
		$selectedController = $this->_serviceManager->getState()->getRoute()->getController();
		if ($controller == $selectedController) {
			$msg = sprintf('Failed asserting last controller used <"%s"> was NOT "%s"', $selectedController, $controller);
			if (!empty($message)) {
				$msg = $message . "\n" . $msg;
			}
			$this->fail($msg);
		}
	}

	/**
	 * Assert that the last handled request used the given action
	 *
	 * @param  string $action
	 * @param  string $message
	 * @return void
	 */
	public function assertAction($action, $message = '')
	{
		$this->_incrementAssertionCount();
		$selectedAction = $this->_serviceManager->getState()->getRoute()->getAction();
		if ($action != $selectedAction) {
			$msg = sprintf('Failed asserting last action used <"%s"> was "%s"', $selectedAction, $action);
			if (!empty($message)) {
				$msg = $message . "\n" . $msg;
			}
			$this->fail($msg);
		}
	}

	/**
	 * Assert that the last handled request did NOT use the given action
	 *
	 * @param  string $action
	 * @param  string $message
	 * @return void
	 */
	public function assertNotAction($action, $message = '')
	{
		$this->_incrementAssertionCount();
		$selectedAction = $this->_serviceManager->getState()->getRoute()->getAction();
		if ($action == $selectedAction) {
			$msg = sprintf('Failed asserting last action used <"%s"> was NOT "%s"', $selectedAction, $action);
			if (!empty($message)) {
				$msg = $message . "\n" . $msg;
			}
			$this->fail($msg);
		}
	}

	/**
	 * Assert that the specified route was used
	 *
	 * @param  string $route
	 * @param  string $message
	 * @return void
	 */
	public function assertRoute($route, $message = '')
	{
		$this->_incrementAssertionCount();
		$router = $this->frontController->getRouter();
		$selectedRoute = $this->_serviceManager->getState()->getRoute()->getName();
		if ($route != $selectedRoute) {
			$msg = sprintf('Failed asserting matched route was "%s", actual route is %s', $route, $selectedRoute);
			if (!empty($message)) {
				$msg = $message . "\n" . $msg;
			}
			$this->fail($msg);
		}
	}

	/**
	 * Assert that the route matched is NOT as specified
	 *
	 * @param  string $route
	 * @param  string $message
	 * @return void
	 */
	public function assertNotRoute($route, $message = '')
	{
		$this->_incrementAssertionCount();
		$router = $this->frontController->getRouter();
		$selectedRoute = $this->_serviceManager->getState()->getRoute()->getName();
		if ($route == $selectedRoute) {
			$msg = sprintf('Failed asserting route matched was NOT "%s"', $route);
			if (!empty($message)) {
				$msg = $message . "\n" . $msg;
			}
			$this->fail($msg);
		}
	}

	/**
	 * Retrieve DOM query object
	 *
	 * @return Zend_Dom_Query
	 */
	public function getQuery()
	{
		if (null === $this->_query) {
			require_once 'Zend/Dom/Query.php';
			$this->_query = new Zend_Dom_Query;
		}
		return $this->_query;
	}

	/**
	 * Increment assertion count
	 *
	 * @return void
	 */
	protected function _incrementAssertionCount()
	{
		$stack = debug_backtrace();
		foreach (debug_backtrace() as $step) {
			if (isset($step['object'])
				&& $step['object'] instanceof PHPUnit_Framework_TestCase
			) {
				if (version_compare(PHPUnit_Runner_Version::id(), '3.3.0', 'lt')) {
					break;
				} elseif (version_compare(PHPUnit_Runner_Version::id(), '3.3.3', 'lt')) {
					$step['object']->incrementAssertionCounter();
				} else {
					$step['object']->addToAssertionCount(1);
				}
				break;
			}
		}
	}

}