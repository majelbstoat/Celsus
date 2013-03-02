<?php

class Celsus_Mixer_Operation_Sort_ByLabelTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeStableSortedNaturallyByLabel() {
		$operator = new Celsus_Mixer_Operation_Sort_ByLabel(array(
			'count' => 4
		));

		$sourceDefinition = array(
			"A" => array("D", "B", "A", "C"),
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testShouldBeAbleToSortDescending() {
		$operator = new Celsus_Mixer_Operation_Sort_ByLabel(array(
			'direction' => SORT_DESC
		));

		$sourceDefinition = array(
			"A" => array("D", "B", "A", "C"),
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"D", "C", "B", "A"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}
}