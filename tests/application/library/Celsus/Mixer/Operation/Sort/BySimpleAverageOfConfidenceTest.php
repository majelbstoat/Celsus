<?php

class Celsus_Mixer_Operation_Sort_BySimpleAverageOfConfidenceTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeStableSortedBySimpleAverageOfConfidence() {
		$operator = new Celsus_Mixer_Operation_Sort_BySimpleAverageOfConfidence();

		$sourceDefinition = array(
			//            5    10   15   20
			"A" => array("A", "B", "C", "D"),
			"B" => array("B", "A", "E"),
			"C" => array("D", "A", "B")
		);

		// A: 25 / 3 = 8.3
		// B: 30 / 3 = 10
		// C: 15 / 1 = 15
		// D: 25 / 2 = 12.5
		// E: 15 / 1 = 15

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 5, -5);

		$expected = array(
			"C", "E", "D", "B", "A"
		);

		$results = $operator->process($sourceData);

		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testShouldBeAbleToSortAscending() {
		$operator = new Celsus_Mixer_Operation_Sort_BySimpleAverageOfConfidence(array(
			'direction' => SORT_ASC
		));

		$sourceDefinition = array(
			//            5    10   15   20
			"A" => array("A", "B", "C", "D"),
			"B" => array("B", "A", "E"),
			"C" => array("D", "A", "B")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 5, -5);

		$expected = array(
			"A", "B", "D", "C", "E"
		);

		$results = $operator->process($sourceData);

		$this->assertSame($expected, $results->extractLabelsToArray());

	}
}