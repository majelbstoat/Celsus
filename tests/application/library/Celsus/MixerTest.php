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

		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testTheResultOfMixingShouldBeUsableAsAMixingSource() {

		$mixer = new Celsus_Mixer('Celsus_Test_Mixer_Source');

		// Generate a simple intermediate result with two entries.
		$sourceDefinition = array(
			"A" => array("A", "B"),
		);

		$sourceData = Celsus_Test_Mixer_Source::generateSimpleSourceSet($sourceDefinition);

		$mixer->setSources($sourceData)->setOperators(array(
			new Celsus_Mixer_Operation_Simple(2),
		));

		$partialA = $mixer->mix(2);

		// Generate a second simple intermediate result with two entries.
		$sourceDefinition = array(
			"B" => array("C", "D"),
		);

		$sourceData = Celsus_Test_Mixer_Source::generateSimpleSourceSet($sourceDefinition);

		$mixer->setSources($sourceData)->setOperators(array(
			new Celsus_Mixer_Operation_Simple(2),
		));

		$partialB = $mixer->mix(2);

		// Mix the two intermediate results together.
		$mixer->setSources(array(
			$partialA,
			$partialB
		))->setOperators(array(
			new Celsus_Mixer_Operation_RoundRobin(4)
		));

		$results = $mixer->mix(4);

		$expected = array(
			"A", "C", "B", "D"
		);

		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testWhenRequestingAnExactCountABackfillMethodShouldBeSpecified() {
		$this->markTestIncomplete("Not implemented yet.");
	}
}