<?php

class Celsus_Set_Operation_UnionTest extends PHPUnit_Framework_TestCase {

	/**
	 * The union object we test with.
	 *
	 * @var Celsus_Set_Operation_Union
	 */
	private $_union = null;

	public function setUp() {
		$this->_union = new Celsus_Set_Operation_Union('Celsus_Test_Set_Interface');
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testUndefinedMethodOnSetObjectThrowsException() {
		return $this->_union->unavailable_method();
	}

	/**
	 * @expectedException Celsus_Exception
	 *
	 * Self-shunted - this class does not implement the required interface.
	 */
	public function testCannotAddElementThatDoesntMeetInterfaceRequirements() {
		$this->_union->addElement($this);
	}

	public function testNoElementsReturnsFalse() {
		$this->assertFalse($this->_union->acceptable());
	}

	public function testUnionOfTwoPassesReturnsTrue() {
		$this->_union->addElement(new Celsus_Test_Set_Acceptable());
		$this->_union->addElement(new Celsus_Test_Set_Acceptable());
		$this->assertTrue($this->_union->acceptable());
	}

	public function testUnionOfPassAndFailReturnsTrue() {
		$this->_union->addElement(new Celsus_Test_Set_Acceptable());
		$this->_union->addElement(new Celsus_Test_Set_Unacceptable());
		$this->assertTrue($this->_union->acceptable());
	}

	public function testUnionOfTwoFailsReturnsFalse() {
		$this->_union->addElement(new Celsus_Test_Set_Unacceptable());
		$this->_union->addElement(new Celsus_Test_Set_Unacceptable());
		$this->assertFalse($this->_union->acceptable());
	}
}

?>