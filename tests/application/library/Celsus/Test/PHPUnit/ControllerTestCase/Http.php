<?php

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

class Celsus_Test_PHPUnit_ControllerTestCase_Http extends Zend_Test_PHPUnit_ControllerTestCase {

	/**
	 * @var Zend_Application
	 */
	protected $_application;

	protected function _bootstrap() {
		$this->_application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/common.ini');
		$this->_application->setOptions(array(
			'config' => APPLICATION_PATH . '/configs/web.ini'
		))->bootstrap();
	}

	public function setUp() {
		$this->bootstrap = array(
			$this, '_bootstrap'
		);
		parent::setUp();
	}
}
?>