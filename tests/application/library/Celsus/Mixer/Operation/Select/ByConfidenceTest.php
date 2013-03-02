<?php

class Celsus_Mixer_Operation_Select_ByConfidenceTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testShouldBeAbleToSpecifyAMinimumConfidenceOnly() {
		$operator = new Celsus_Mixer_Operation_Select_ByConfidence(array(
			'minimum' => 15
		));

		$sourceDefinition = array(
			//            5    10   15   20
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 5, -5);

		$expected = array(
			"C", "D", "G", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testShouldBeAbleToSpecifyAMaximumConfidenceOnly() {
		$operator = new Celsus_Mixer_Operation_Select_ByConfidence(array(
			'maximum' => 10
		));

		$sourceDefinition = array(
			//            5    10   15   20
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 5, -5);

		$expected = array(
			"A", "B", "E", "F", "H", "I"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testShouldBeAbleToSpecifyAMinimumAndAMaximumConfidence() {
		$operator = new Celsus_Mixer_Operation_Select_ByConfidence(array(
			'minimum' => 9,
			'maximum' => 16
		));

		$sourceDefinition = array(
			//            5    10   15   20
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 5, -5);

		$expected = array(
			"B", "C", "F", "G", "I", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testNotSpecifyingAMinimumOrAMaximumShouldThrowAnException() {
		$operator = new Celsus_Mixer_Operation_Select_ByConfidence();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$results = $operator->process($sourceData);
	}
}