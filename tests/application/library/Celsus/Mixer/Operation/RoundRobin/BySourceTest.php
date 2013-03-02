<?php

class Celsus_Mixer_Operation_RoundRobin_BySourceTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeCombinedUsingARoundRobinStrategy() {
		$operator = new Celsus_Mixer_Operation_RoundRobin_BySource();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "E", "H", "B", "F", "I", "C", "G", "J", "D"
		);

		$results = $operator->process($sourceData);
		$this->assertSame('Celsus_Mixer_Component_Group', get_class($results));
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testInsufficientResultsShouldCauseAllItemsToBeUsed() {
		$operator = new Celsus_Mixer_Operation_RoundRobin_BySource(array(
			'count' => 20
		));

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "E", "H", "B", "F", "I", "C", "G", "J", "D"
		);

		// Test that the available results are combined, and returns what is possible.

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testTooManyResultsShouldCauseExcessResultsToBeDiscarded() {
		$operator = new Celsus_Mixer_Operation_RoundRobin_BySource(array(
			'count' => 5
		));

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "E", "H", "B", "F"
		);

		// Test that the available results are combined, and returns what is possible.

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testOneSourceHavingLessResultsThanOthersShouldNotCauseIssues() {
		$operator = new Celsus_Mixer_Operation_RoundRobin_BySource(array(
			'count' => 10
		));

		$sourceA = array("A", "B");
		$sourceB = array("C", "D", "E", "F");
		$sourceC = array("G", "H", "I", "J");

		$sourceData = array($sourceA, $sourceB, $sourceC);

		$sourceDefinition = array(
			"A" => array("A", "B"),
			"B" => array("C", "D", "E", "F"),
			"C" => array("G", "H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "C", "G", "B", "D", "H", "E", "I", "F", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testSimilarResultsFromDifferentSourcesShouldBeMerged() {
		$operator = new Celsus_Mixer_Operation_RoundRobin_BySource(array(
			'count' => 10
		));

		$sourceDefinition = array(
			"A" => array("A", "B"),
			"B" => array("C", "D", "A", "B"),
			"C" => array("G", "H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "C", "G", "B", "D", "H", "I", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testSuppliedResultSetShouldNotBeModified() {
		$operator = new Celsus_Mixer_Operation_RoundRobin_BySource(array(
			'count' => 10
		));

		$sourceDefinition = array(
			"A" => array("A", "B"),
			"B" => array("C", "D", "A", "B"),
			"C" => array("G", "H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = Celsus_Test_Mixer_Component::countSources($sourceData);

		$results = $operator->process($sourceData);

		$actual = Celsus_Test_Mixer_Component::countSources($sourceData);

		$this->assertSame($expected, $actual);
	}

	public function testShouldBeAbleToDefineAStepProgramToVaryTheAmountTakenFromEachSource() {
		$operator = new Celsus_Mixer_Operation_RoundRobin_BySource(array(
			'steps' => array(
				Celsus_Test_Mixer_Source::SOURCE_TYPE_A => 2,
				Celsus_Test_Mixer_Source::SOURCE_TYPE_B => 1,
				Celsus_Test_Mixer_Source::SOURCE_TYPE_C => 1
			)
		));

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "B", "E", "H", "C", "D", "F", "I", "G", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame('Celsus_Mixer_Component_Group', get_class($results));
		$this->assertSame($expected, $results->extractLabelsToArray());
	}
}