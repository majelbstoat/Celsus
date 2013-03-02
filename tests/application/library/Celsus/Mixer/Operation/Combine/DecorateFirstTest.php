<?php

class Celsus_Mixer_Operation_Combine_DecorateFirstTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsCanBeCombinedByTakingTheFirstItemAndAppendingSubsequentSources() {
		$operator = new Celsus_Mixer_Operation_Combine_DecorateFirst();

		$sourceDefinition = array(
			//            10   20   30   40
			"A" => array("A", "B", "C", "D"),
			"B" => array("C", "A", "E"),
			"C" => array("F", "D", "B")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 10, -10);

		$confidences = array(
			"A" => 10,
			"B" => 20,
			"C" => 30,
			"D" => 40,
			"E" => 30,
			"F" => 10
		);

		$sources = array(
			"A" => array("A", "B"),
			"B" => array("A", "C"),
			"C" => array("A", "B"),
			"D" => array("A", "C"),
			"E" => array("B"),
			"F" => array("C"),
		);

		$results = $operator->process($sourceData);

		$this->assertSame($confidences, $results->extractConfidencesToArray());
		$this->assertSame($sources, $results->extractSourcesToArray());
	}
}