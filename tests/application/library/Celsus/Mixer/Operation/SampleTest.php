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
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));

	}

	public function testAttemptingToProcessWithoutAtLeastOneSourceShouldThrowAnException() {
		$this->markTestIncomplete("Not implemented yet.");
	}

	public function testAtLeastOneResultShouldBeSampledIn() {
		$this->markTestIncomplete("Not implemented yet.");
	}

	public function testSpecifyingASamplingRateHigherThan100ShouldThrowAnException() {
		$this->markTestIncomplete("Not implemented yet.");
	}

	public function testShouldBeAbleToSpecifyMoreThanOneSource() {
		$this->markTestIncomplete("Not implemented yet.");
	}

	public function testAttemptingToProcessWithNoSpecifiedSampleRateShouldThrowAnException() {
		$this->markTestIncomplete("Not implemented yet.");
	}
}