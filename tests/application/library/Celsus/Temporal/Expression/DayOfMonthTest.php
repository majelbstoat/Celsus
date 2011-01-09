<?php

class Celsus_Temporal_Expression_DayOfMonthTest extends PHPUnit_Framework_TestCase {

	/**
	 * The expression object we test with.
	 *
	 * @var Celsus_Temporal_Expression_DayOfMonth
	 */
	private $_expression = null;

	public function setUp() {
		// Last Monday in the month.
		$this->_expression = new Celsus_Temporal_Expression_DayOfMonth(24);
	}

	public function testFebruary24th2010Is24th() {
		$this->assertTrue($this->_expression->includes('2010-02-24'));
	}

	public function testFebruary25th2010IsNot24th() {
		$this->assertFalse($this->_expression->includes('2010-02-25'));
	}

}

?>