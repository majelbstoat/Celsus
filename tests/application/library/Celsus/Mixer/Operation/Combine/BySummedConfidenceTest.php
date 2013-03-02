<?php

class Celsus_Mixer_Operation_Combine_BySummedConfidenceTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testShouldBeAbleToCombineResultsBySum() {
		$operator = new Celsus_Mixer_Operation_Combine_BySummedConfidence();

		$sourceDefinition = array(
			//            5    10   15   20
			"A" => array("A", "B", "D", "C"),
			"B" => array("D", "A", "B"),
			"C" => array("E", "A", "C")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 5, -5);

		$expectedConfidences = array(
			"A" => 25,
			"B" => 25,
			"D" => 20,
			"C" => 35,
			"E" => 5
		);

		$expectedSources = array(
			"A" => array("A", "B", "C"),
			"B" => array("A", "B"),
			"D" => array("A", "B"),
			"C" => array("A", "C"),
			"E" => array("C")
		);

		$results = $operator->process($sourceData);

		$this->assertSame($expectedConfidences, $results->extractConfidencesToArray());
		$this->assertSame($expectedSources, $results->extractSourcesToArray());
	}
}