<?php

class Celsus_Mixer_Operation_Boost_BySourceTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function estResultsShouldBeBoostedBySpecifiedFactorForEachSource() {
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

	public function testIfAResultHasComeFromMultipleSourcesACombinedBoostFactorShouldBeUsed() {
		$sourceDefinition = array(
			//            2    4    6    8
			"A" => array("A", "B", "C", "D"),
			"B" => array("D", "C", "E"),
			"C" => array("F", "A", "G")
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition, 2, -2);

		$operator = new Celsus_Mixer_Operation_Combine_BySummedConfidence();

		$partialA = $operator->process($sourceData);

		$confidences = array(
			"A" => 6,
			"B" => 4,
			"C" => 10,
			"D" => 10,
			"E" => 6,
			"F" => 2,
			"G" => 6,
		);

		$sources = array(
			"A" => array("A", "C"),
			"B" => array("A"),
			"C" => array("A", "B"),
			"D" => array("A", "B"),
			"E" => array("B"),
			"F" => array("C"),
			"G" => array("C"),
		);

		$this->assertSame($confidences, $partialA->extractConfidencesToArray());
		$this->assertSame($sources, $partialA->extractSourcesToArray());

		$operator = new Celsus_Mixer_Operation_Boost_BySource(array(
			'boost' => array(
				Celsus_Test_Mixer_Source::SOURCE_TYPE_A => 3,
				Celsus_Test_Mixer_Source::SOURCE_TYPE_B => 2,
				Celsus_Test_Mixer_Source::SOURCE_TYPE_C => 5,
			)
		));

		$results = $operator->process($partialA);

		$confidences = array(
			"A" => 90,
			"B" => 12,
			"C" => 60,
			"D" => 60,
			"E" => 12,
			"F" => 10,
			"G" => 30
		);

		$this->assertSame($confidences, $results->extractConfidencesToArray());
		$this->assertSame($sources, $results->extractSourcesToArray());
	}
}