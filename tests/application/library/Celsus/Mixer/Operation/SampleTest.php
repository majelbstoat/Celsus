<?php

class Celsus_Mixer_Operation_SampleTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testSampledResultsShouldBeMixedInUsingTheSpecifiedSources() {
		$sampleData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("X", "Y", "Z"),
		));

		$operator = new Celsus_Mixer_Operation_Sample(array(
			'samplePercent' => 30,
			'sources' => array(
				$sampleData
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J"),
		));

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "X", "Y", "Z"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testAttemptingToProcessWithoutAtLeastOneSourceShouldThrowAnException() {
		$operator = new Celsus_Mixer_Operation_Sample(array(
			'samplePercent' => 30,
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J"),
		));

		$results = $operator->process($sourceData);
	}

	public function testAtLeastOneResultShouldBeSampledIn() {
		$sampleData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("X", "Y", "Z"),
		));

		$operator = new Celsus_Mixer_Operation_Sample(array(
			'samplePercent' => 1,
			'sources' => array(
				$sampleData
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D"),
		));

		$expected = array(
			"A", "B", "C", "X"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testSpecifyingASamplingRateHigherThan100ShouldThrowAnException() {
		$sampleData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("X", "Y", "Z"),
		));

		$operator = new Celsus_Mixer_Operation_Sample(array(
			'samplePercent' => 101,
			'sources' => array(
				$sampleData
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D"),
		));

		$results = $operator->process($sourceData);
	}

	public function testShouldBeAbleToSpecifyMoreThanOneSource() {
		$sampleAData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("X", "Y", "Z"),
		));

		$sampleBData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"C" => array("S", "T", "U"),
		));

		$operator = new Celsus_Mixer_Operation_Sample(array(
			'samplePercent' => 60,
			'sources' => array(
				$sampleAData,
				$sampleBData
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J"),
		));

		$expected = array(
			"A", "B", "C", "D", "X", "Y", "Z", "S", "T", "U"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testAttemptingToProcessWithNoSpecifiedSampleRateShouldThrowAnException() {
		$sampleData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("X", "Y", "Z"),
		));

		$operator = new Celsus_Mixer_Operation_Sample(array(
			'sources' => array(
				$sampleData
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D"),
		));

		$results = $operator->process($sourceData);
	}
}