<?php

class Celsus_Mixer_Operation_Sort_ByConfidenceTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeStableSortedByConfidence() {
		$operator = new Celsus_Mixer_Operation_Sort_ByConfidence();

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 5, -5);

		$expected = array(
			"D", "C", "G", "J", "B", "F", "I", "A", "E", "H"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testShouldBeAbleToSortAscending() {

		$operator = new Celsus_Mixer_Operation_Sort_ByConfidence(array(
			'direction' => SORT_ASC
		));

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 5, -5);

		$expected = array(
			"A", "E", "H", "B", "F", "I", "C", "G", "J", "D"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());

	}
}