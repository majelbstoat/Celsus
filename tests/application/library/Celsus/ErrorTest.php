<?php
class Celsus_ErrorTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		error_reporting(E_ALL);
		set_error_handler(array('Celsus_Error', 'handle'));
	}

	public function tearDown() {
		restore_error_handler();
	}
	
	/**
	 * @expectedException Celsus_Exception
	 */
	public function testUserErrorThrowsExceptionInstead() {
		trigger_error("This is an error", E_USER_ERROR);
	}
	
	/**
	 * @expectedException Celsus_Exception
	 */
	public function testUserWarningThrowsExceptionInstead() {
		trigger_error("This is a warning", E_USER_WARNING);
	}
	
	/**
	 * @expectedException Celsus_Exception
	 */
	public function testUserNoticeThrowsExceptionInstead() {
		trigger_error("This is a notice", E_USER_NOTICE);
	}
	
}