<?php

class Celsus_Mixer_CombinationStrategy_RoundRobinTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeCombinedUsingARoundRobinStrategy() {
		$combinator = new Celsus_Mixer_CombinationStrategy_RoundRobin();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = array(
			"A", "E", "H", "B", "F", "I", "C", "G", "J", "D"
		);

		$results = $combinator->combine($sourceData, 10);
		$this->assertSame($expected, array_keys($results));
	}

	public function testInsufficientResultsShouldCauseAllItemsToBeUsed() {
		$combinator = new Celsus_Mixer_CombinationStrategy_RoundRobin();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = array(
			"A", "E", "H", "B", "F", "I", "C", "G", "J", "D"
		);

		// Test that the available results are combined, and returns what is possible.

		$results = $combinator->combine($sourceData, 20);
		$this->assertSame($expected, array_keys($results));
	}

	public function testTooManyResultsShouldCauseExcessResultsToBeDiscarded() {
		$combinator = new Celsus_Mixer_CombinationStrategy_RoundRobin();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = array(
			"A", "E", "H", "B", "F"
		);

		// Test that the available results are combined, and returns what is possible.

		$results = $combinator->combine($sourceData, 5);
		$this->assertSame($expected, array_keys($results));
	}

	public function testOneSourceHavingLessResultsThanOthersShouldNotCauseIssues() {
		$combinator = new Celsus_Mixer_CombinationStrategy_RoundRobin();

		$sourceA = array("A", "B");
		$sourceB = array("C", "D", "E", "F");
		$sourceC = array("G", "H", "I", "J");

		$sourceData = array($sourceA, $sourceB, $sourceC);

		$sourceDefinition = array(
			"A" => array("A", "B"),
			"B" => array("C", "D", "E", "F"),
			"C" => array("G", "H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = array(
			"A", "C", "G", "B", "D", "H", "E", "I", "F", "J"
		);

		$results = $combinator->combine($sourceData, 10);
		$this->assertSame($expected, array_keys($results));
	}

	public function testSimilarResultsFromDifferentSourcesShouldBeMerged() {
		$combinator = new Celsus_Mixer_CombinationStrategy_RoundRobin();

		$sourceDefinition = array(
			"A" => array("A", "B"),
			"B" => array("C", "D", "A", "B"),
			"C" => array("G", "H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = array(
			"A", "C", "G", "B", "D", "H", "I", "J"
		);

		$results = $combinator->combine($sourceData, 10);
		$this->assertSame($expected, array_keys($results));
	}

	public function testSuppliedResultSetShouldNotBeModified() {
		$combinator = new Celsus_Mixer_CombinationStrategy_RoundRobin();

		$sourceDefinition = array(
			"A" => array("A", "B"),
			"B" => array("C", "D", "A", "B"),
			"C" => array("G", "H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_SourceResult::generateSimpleSet($sourceDefinition);

		$expected = Celsus_Test_Mixer_SourceResult::countSources($sourceData);

		$results = $combinator->combine($sourceData, 10);

		$actual = Celsus_Test_Mixer_SourceResult::countSources($sourceData);

		$this->assertSame($expected, $actual);

	}
}