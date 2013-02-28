<?php

class Celsus_Mixer_Operation_BackfillTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeBackfilledUsingTheSpecifiedSources() {
		$backfillData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		));

		$operator = new Celsus_Mixer_Operation_Backfill(array(
			'minimum' => 10,
			'sources' => array(
				$backfillData
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D"),
		));

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testIfThereAreAlreadyEnoughResultsTheBackfillSourcesShouldNotHaveToYieldAnything() {

		// Define a mock source on which yield should not be called.
		$stub = $this->getMock('Celsus_Test_Mixer_Source_A', array('yield'), array(), '', false);
		$stub->expects($this->never())->method('yield');

		$operator = new Celsus_Mixer_Operation_Backfill(array(
			'minimum' => 4,
			'sources' => array(
				$stub
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D"),
		));

		$expected = array(
			"A", "B", "C", "D"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testShouldBeAbleToSpecifyANumberOfRetryAttemptsIfTheFirstBackfillDoesntYieldEnoughResults() {

		$backfillBData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("E", "F", "G"),
		));
		$backfillCData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"C" => array("H", "I", "J", "K", "L")
		));

		// Define a mock source that yields backfill data twice.
		$stub = $this->getMock('Celsus_Test_Mixer_Source_A', array('yield'), array(), '', false);
		$stub->expects($this->at(0))->method('yield')->will($this->returnValue($backfillBData));
		$stub->expects($this->at(1))->method('yield')->will($this->returnValue($backfillCData));

		$operator = new Celsus_Mixer_Operation_Backfill(array(
			'minimum' => 10,
			'sources' => array(
				$stub
			),
			'tries' => 2
		));

		$sourceDefinition = array(
			"A" => array("A", "B", "C", "D"),
		);

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup($sourceDefinition);

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testAttemptingToProcessWithNoSpecifiedMinimumShouldThrowAnException() {
		$backfillData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("E", "F", "G"),
		));

		// No minimum specified.
		$operator = new Celsus_Mixer_Operation_Backfill(array(
			'sources' => array(
				$backfillData
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D"),
		));

		$results = $operator->process($sourceData);
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testAttemptingToProcessWithoutAtLeastOneSourceShouldThrowAnException() {
		$operator = new Celsus_Mixer_Operation_Backfill(array(
			'minimum' => 20
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D"),
		));

		$results = $operator->process($sourceData);
	}

	public function testShouldBeAbleToSpecifyMoreThanOneSource() {
		$backfillBData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("E", "F", "G"),
		));
		$backfillCData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"C" => array("H", "I", "J")
		));

		$operator = new Celsus_Mixer_Operation_Backfill(array(
			'minimum' => 10,
			'sources' => array(
				$backfillBData,
				$backfillCData
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D"),
		));

		$expected = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}

	public function testBackfillingShouldNotIntroduceDuplicates() {
		$backfillData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"B" => array("E", "F", "G"),
			"C" => array("H", "I", "J")
		));

		$operator = new Celsus_Mixer_Operation_Backfill(array(
			'minimum' => 10,
			'sources' => array(
				$backfillData
			)
		));

		$sourceData = Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			"A" => array("A", "B", "C", "D", "F", "H"),
		));

		$expected = array(
			"A", "B", "C", "D", "F", "H", "E", "G", "I", "J"
		);

		$results = $operator->process($sourceData);
		$this->assertSame($expected, Celsus_Test_Mixer_Component::extractLabelsToArray($results));
	}
}