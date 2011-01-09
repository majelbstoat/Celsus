<?php

class Celsus_Temporal_Expression_MonthsFromStartTest extends PHPUnit_Framework_TestCase {

	/**
	 * The expression object we test with.
	 *
	 * @var Celsus_Temporal_Expression_MonthsFromStart
	 */
	private $_expression = null;

	public function setUp() {
		// Last Monday in the month.
		$this->_expression = new Celsus_Temporal_Expression_MonthsFromStart('2010-01-01', 2);
	}

	public function testDatesInIntervalAreIncludedInExpression() {
		$this->assertTrue($this->_expression->includes('2010-03-24'));
		$this->assertTrue($this->_expression->includes('2010-05-24'));
	}

	public function testDatesNotInIntervalAreNotIncludedInExpression() {
		$this->assertFalse($this->_expression->includes('2010-04-24'));
	}

}

?>