<?php

class Celsus_Mixer_Operation_Boost_BySourceTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeBoostedBySpecifiedFactorForEachSource() {
		$operator = new Celsus_Mixer_Operation_Boost_BySource(array(
			'boost' => array(
				Celsus_Test_Mixer_Source::SOURCE_TYPE_A => 1.1,
				Celsus_Test_Mixer_Source::SOURCE_TYPE_B => 2.2,
				Celsus_Test_Mixer_Source::SOURCE_TYPE_C => 1.5,
			)
		));

		$sourceDefinition = array(
			//            10   20   30   40
			"A" => array("A", "B", "C", "D"),
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 10, -10);

		$expected = array(
			"A" => (double) 11,
			"B" => (double) 22,
			"C" => (double) 33,
			"D" => (double) 44,
			"E" => (double) 22,
			"F" => (double) 44,
			"G" => (double) 66,
			"H" => (double) 15,
			"I" => (double) 30,
			"J" => (double) 45,
		);

		$results = $operator->process($sourceData);

		$this->assertSame($expected, $results->extractConfidencesToArray());
	}
}