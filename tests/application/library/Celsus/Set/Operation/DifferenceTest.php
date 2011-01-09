<?php

class Celsus_Set_Operation_DifferenceTest extends PHPUnit_Framework_TestCase {

	/**
	 * The difference object we test with.
	 *
	 * @var Celsus_Set_Operation_Difference
	 */
	private $_difference = null;

	public function setUp() {
		$this->_difference = new Celsus_Set_Operation_Difference('Celsus_Test_Set_Interface');
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testUndefinedMethodOnSetObjectThrowsException() {
		return $this->_difference->unavailable_method();
	}

	/**
	 * @expectedException Celsus_Exception
	 *
	 * Self-shunted - this class does not implement the required interface.
	 */
	public function testCannotAddElementThatDoesntMeetInterfaceRequirements() {
		$this->_difference->addElement($this);
	}

	public function testNoElementsReturnsFalse() {
		$this->assertFalse($this->_difference->acceptable());
	}

	public function testExcludePassAndIncludeFailReturnsFalse() {
		$this->_difference->addExclude(new Celsus_Test_Set_Acceptable());
		$this->_difference->addInclude(new Celsus_Test_Set_Unacceptable());
		$this->assertFalse($this->_difference->acceptable());
	}

	public function testIncludePassAndIncludeFailReturnsFalse() {
		$this->_difference->addInclude(new Celsus_Test_Set_Acceptable());
		$this->_difference->addInclude(new Celsus_Test_Set_Unacceptable());
		$this->assertFalse($this->_difference->acceptable());
	}

	public function testIncludePassAndExcludeFailReturnsTrue() {
		$this->_difference->addInclude(new Celsus_Test_Set_Acceptable());
		$this->_difference->addExclude(new Celsus_Test_Set_Unacceptable());
		$this->assertTrue($this->_difference->acceptable());
	}

	public function testExcludePassAndExcludeFailReturnsTrue() {
		$this->_difference->addExclude(new Celsus_Test_Set_Acceptable());
		$this->_difference->addExclude(new Celsus_Test_Set_Unacceptable());
		$this->assertFalse($this->_difference->acceptable());
	}
}

?>