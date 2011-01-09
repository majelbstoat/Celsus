<?php

class Celsus_Temporal_Expression_DayInMonthTest extends PHPUnit_Framework_TestCase {

	/**
	 * The expression object we test with.
	 *
	 * @var Celsus_Temporal_Expression_DayInMonth
	 */
	private $_expression = null;

	public function setUp() {
		// Last Monday in the month.
		$this->_expression = new Celsus_Temporal_Expression_DayInMonth(Celsus_Temporal_Expression::MONDAY, -1);
	}

	public function testFebruary22nd2010IsLastMonday() {
		$this->assertTrue($this->_expression->includes('2010-02-22'));
	}
}

?>