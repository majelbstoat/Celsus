<?php

class Celsus_MixerTest extends Celsus_TestCase {

	// Tests

	public function testMultipleOperationsShouldBeSupported() {

		$mixer = new Celsus_Mixer('Celsus_Test_Mixer_Source');

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Source::generateSimpleComponentGroupSet($sourceDefinition);

		$mixer->setSources($sourceData)->setOperators(array(
			new Celsus_Mixer_Operation_Select_Simple(array(
				'count' => 5
			)),
			new Celsus_Mixer_Operation_RoundRobin_BySource(),
		));

		$expected = array(
			"A", "E", "B", "C", "D"
		);

		$results = $mixer->mix();

		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testTheResultOfMixingShouldBeUsableAsAMixingSource() {

		$mixer = new Celsus_Mixer('Celsus_Test_Mixer_Source');

		// Generate a simple intermediate result with two entries.
		$sourceDefinition = array(
			"A" => array("A", "B"),
		);

		$sourceData = Celsus_Test_Mixer_Source::generateSimpleComponentGroupSet($sourceDefinition);

		$mixer->setSources($sourceData)->setOperators(array(
			new Celsus_Mixer_Operation_Select_Simple(),
		));

		$partialA = $mixer->mix();

		$this->assertObjectIsInstanceOf($partialA, 'Celsus_Mixer_Source_Interface');

		// Generate a second simple intermediate result with two entries.
		$sourceDefinition = array(
			"B" => array("C", "D"),
		);

		$sourceData = Celsus_Test_Mixer_Source::generateSimpleComponentGroupSet($sourceDefinition);

		$mixer->setSources($sourceData)->setOperators(array(
			new Celsus_Mixer_Operation_Select_Simple(),
		));

		$partialB = $mixer->mix();

		// Mix the two intermediate results together.
		$mixer->setSources(array(
			$partialA,
			$partialB
		))->setOperators(array(
			new Celsus_Mixer_Operation_RoundRobin_BySource()
		));

		$results = $mixer->mix();

		$expected = array(
			"A", "C", "B", "D"
		);

		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testNotSpecifyingAnySourcesShouldResultInAllSourcesBeingUsed() {
		$mixer = new Celsus_Mixer('Celsus_Test_Mixer_Source');

		Celsus_Test_Mixer_Source_A::setDefaultResults(array('U', 'W'));
		Celsus_Test_Mixer_Source_B::setDefaultResults(array('T', 'V'));
		Celsus_Test_Mixer_Source_C::setDefaultResults(array('X', 'Y'));

		$results = $mixer->mix();

		$expected = array(
			"U", "W", "T", "V", "X", "Y"
		);

		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testSourcesCanBeConfigured() {
		$mixer = new Celsus_Mixer('Celsus_Test_Mixer_Source');

		Celsus_Test_Mixer_Source_A::setDefaultResults(array('U', 'W', 'A', 'B', 'C'));
		Celsus_Test_Mixer_Source_B::setDefaultResults(array('T', 'V', 'D', 'E', 'F'));
		Celsus_Test_Mixer_Source_C::setDefaultResults(array('X', 'Y', 'G', 'H', 'I'));

		$results = $mixer->configureSource(Celsus_Test_Mixer_Source::SOURCE_TYPE_A, array(
			'count' => 2
		))->configureSource(Celsus_Test_Mixer_Source::SOURCE_TYPE_C, array(
			'count' => 2
		))->mix();

		$expected = array(
			"U", "W", "T", "V", "D", "E", "F", "X", "Y"
		);

		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}
}