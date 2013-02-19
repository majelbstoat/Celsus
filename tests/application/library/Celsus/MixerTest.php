<?php

class Celsus_MixerTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testMultipleOperationsShouldBeSupported() {

		$mixer = new Celsus_Mixer('Celsus_Test_Mixer_Source');

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Source::generateSimpleSourceSet($sourceDefinition);

		$mixer->setSources($sourceData)->setOperators(array(
			new Celsus_Mixer_Operation_Simple(5),
			new Celsus_Mixer_Operation_RoundRobin(10),
		));

		$expected = array(
			"A", "E", "B", "C", "D"
		);

		$results = $mixer->mix(20);

		$this->assertSame($expected, Celsus_Test_Mixer_Source_Result::extractLabelsToArray($results));
	}

	public function testTheResultOfMixingShouldBeUsableAsAMixingSource() {
		$this->markTestIncomplete("Not implemented yet.");
	}

	public function testWhenRequestingAnExactCountABackfillMethodShouldBeSpecified() {
		$this->markTestIncomplete("Not implemented yet.");
	}
}