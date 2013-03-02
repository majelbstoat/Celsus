<?php

class Celsus_Mixer_Operation_Combine_BySimpleAverageOfConfidenceTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeCombinedBySimpleAverageOfConfidence() {
		$operator = new Celsus_Mixer_Operation_Combine_BySimpleAverageOfConfidence();

		$sourceDefinition = array(
			//            5    10   15   20
			"A" => array("A", "B", "C", "D"),
			"B" => array("B", "A", "E"),
			"C" => array("D", "A", "B"),
			"D" => array("A"),
			"E" => array("A"),
			"F" => array("D")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 5, -5);

		$expectedConfidences = array(
			"A" => (double) 7,
			"B" => (double) 10,
			"C" => (int) 15,
			"D" => (double) 10,
			"E" => (int) 15
		);

		$expectedSources = array(
			"A" => array("A", "B", "C", "D", "E"),
			"B" => array("A", "B", "C"),
			"C" => array("A"),
			"D" => array("A", "C", "F"),
			"E" => array("B")
		);

		$results = $operator->process($sourceData);

		$this->assertSame($expectedConfidences, $results->extractConfidencesToArray());
		$this->assertSame($expectedSources, $results->extractSourcesToArray());
	}
}