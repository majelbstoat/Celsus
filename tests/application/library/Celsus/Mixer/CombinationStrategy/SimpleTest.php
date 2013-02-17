<?php

class Celsus_Mixer_CombinationStrategy_SimpleTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeCombinedUsingTheSimpleStrategy() {
		$combinator = new Celsus_Mixer_CombinationStrategy_Simple();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J"
		);

		$results = $combinator->combine($sourceData, 10);
		$this->assertSame($expected, array_keys($results));
	}

	public function testInsufficientResultsShouldCauseAllItemsToBeUsed() {
		$combinator = new Celsus_Mixer_CombinationStrategy_Simple();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J"
		);

		$results = $combinator->combine($sourceData, 20);
		$this->assertSame($expected, array_keys($results));
	}

	public function testTooManyResultsShouldCauseExcessResultsToBeDiscarded() {
		$combinator = new Celsus_Mixer_CombinationStrategy_Simple();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D", "E"
		);

		$results = $combinator->combine($sourceData, 5);
		$this->assertSame($expected, array_keys($results));
	}

	public function testSimilarResultsFromDifferentSourcesShouldBeMerged() {
		$combinator = new Celsus_Mixer_CombinationStrategy_Simple();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "A", "B"),
			"C" => array("F", "G", "H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J"
		);

		$results = $combinator->combine($sourceData, 10);
		$this->assertSame($expected, array_keys($results));
	}
}