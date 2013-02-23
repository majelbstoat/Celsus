<?php

class Celsus_Mixer_Operation_SimpleTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeCombinedUsingTheSimpleStrategy() {
		$operator = new Celsus_Mixer_Operation_Simple(10);

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testInsufficientResultsShouldCauseAllItemsToBeUsed() {
		$operator = new Celsus_Mixer_Operation_Simple(20);

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testTooManyResultsShouldCauseExcessResultsToBeDiscarded() {
		$operator = new Celsus_Mixer_Operation_Simple(5);

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D", "E"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testSimilarResultsFromDifferentSourcesShouldBeMerged() {
		$operator = new Celsus_Mixer_Operation_Simple(10);

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "A", "B"),
			"C" => array("F", "G", "H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}
}