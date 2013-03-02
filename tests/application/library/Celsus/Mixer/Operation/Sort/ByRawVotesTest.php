<?php

class Celsus_Mixer_Operation_Sort_ByRawVotesTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeCombinedByTallyingVotesFromSources() {
		$operator = new Celsus_Mixer_Operation_Sort_ByRawVotes(array(
			'count' => 4
		));

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("A", "C", "G"),
			"C" => array("B", "C", "F")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"C", "A", "B", "D"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}
}