<?php

class Celsus_Temporal_Expression_UntilTest extends PHPUnit_Framework_TestCase {

	/**
	 * The expression object we test with.
	 *
	 * @var Celsus_Temporal_Expression_Until
	 */
	private $_expression = null;

	public function setUp() {
		// Until 6th June 2010.
		$this->_expression = new Celsus_Temporal_Expression_Until('2010-06-02');
	}

	public function testDatesBeforeUntilDateAreIncludedInExpression() {
		$this->assertTrue($this->_expression->includes('2010-06-01'));
	}

	public function testDatesOnOrAfterUntilDateAreNotIncludedInExpression() {
		$this->assertFalse($this->_expression->includes('2010-06-02'));
		$this->assertFalse($this->_expression->includes('2010-06-03'));
	}
}

?>