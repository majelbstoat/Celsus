<?php

class Celsus_Set_Operation_IntersectionTest extends PHPUnit_Framework_TestCase {

	/**
	 * The intersection object we test with.
	 *
	 * @var Celsus_Set_Operation_Intersection
	 */
	private $_intersection = null;

	public function setUp() {
		$this->_intersection = new Celsus_Set_Operation_Intersection('Celsus_Test_Set_Interface');
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testUndefinedMethodOnSetObjectThrowsException() {
		return $this->_intersection->unavailable_method();
	}

	/**
	 * @expectedException Celsus_Exception
	 *
	 * Self-shunted - this class does not implement the required interface.
	 */
	public function testCannotAddElementThatDoesntMeetInterfaceRequirements() {
		$this->_intersection->addElement($this);
	}

	public function testNoElementsReturnsFalse() {
		$this->assertFalse($this->_intersection->acceptable());
	}

	public function testIntersectionOfTwoPassesReturnsTrue() {
		$this->_intersection->addElement(new Celsus_Test_Set_Acceptable());
		$this->_intersection->addElement(new Celsus_Test_Set_Acceptable());
		$this->assertTrue($this->_intersection->acceptable());
	}

	public function testIntersectionOfPassAndFailReturnsFalse() {
		$this->_intersection->addElement(new Celsus_Test_Set_Acceptable());
		$this->_intersection->addElement(new Celsus_Test_Set_Unacceptable());
		$this->assertFalse($this->_intersection->acceptable());
	}

	public function testIntersectionOfTwoFailsReturnsFalse() {
		$this->_intersection->addElement(new Celsus_Test_Set_Unacceptable());
		$this->_intersection->addElement(new Celsus_Test_Set_Unacceptable());
		$this->assertFalse($this->_intersection->acceptable());
	}
}


?>