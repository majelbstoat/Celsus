<?php

class Celsus_Mixer_Operation_RoundRobin_ByPreviousOperationTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testResultsShouldBeSelectedBasedOnPriorOperations() {
		$mixer = new Celsus_Mixer('Celsus_Test_Mixer_Source');

		Celsus_Test_Mixer_Source_A::setDefaultResults(array('U', 'W', 'A', 'B', 'C'));
		Celsus_Test_Mixer_Source_B::setDefaultResults(array('T', 'V', 'D', 'E', 'F'));
		Celsus_Test_Mixer_Source_C::setDefaultResults(array('X', 'Y', 'G', 'H', 'I'));

		$partialA = $mixer->setOperations(array(
			new Celsus_Mixer_Operation_Sort_ByConfidence(array(
				'direction' => SORT_ASC,
				'count' => 10
			))
		))->process();

		$expected = array(
			"C", "F", "I", "B", "E", "H", "A", "D", "G", "W"
		);

		$this->assertSame($expected, $partialA->extractLabelsToArray());

		$partialB = $mixer->setOperations(array(
			new Celsus_Mixer_Operation_Select_Simple(array(
				'count' => 5
			))
		))->process();

		$expected = array(
			"U", "W", "A", "B", "C"
		);

		$this->assertSame($expected, $partialB->extractLabelsToArray());

		// Now, interleave the results from the previous two mixes.
		$results = $mixer->setSources(array(
			$partialA,
			$partialB
		))->setOperations(array(
			new Celsus_Mixer_Operation_RoundRobin_ByPreviousOperation(array(
				'count' => 10
			)),
		))->process();

		$expected = array(
			"C", "U", "F", "W", "I", "A", "B", "E", "H", "D"
		);

		$this->assertSame($expected, $results->extractLabelsToArray());
	}

	public function testShouldBeAbleToDefineAStepProgramToVaryTheAmountTakenFromEachSource() {
		$mixer = new Celsus_Mixer('Celsus_Test_Mixer_Source');

		Celsus_Test_Mixer_Source_A::setDefaultResults(array('U', 'W', 'A', 'B', 'C'));
		Celsus_Test_Mixer_Source_B::setDefaultResults(array('T', 'V', 'D', 'E', 'F'));
		Celsus_Test_Mixer_Source_C::setDefaultResults(array('X', 'Y', 'G', 'H', 'I'));

		$partialA = $mixer->setOperations(array(
			new Celsus_Mixer_Operation_Sort_ByConfidence(array(
				'direction' => SORT_ASC,
				'count' => 10
			))
		))->process();

		$expected = array(
			"C", "F", "I", "B", "E", "H", "A", "D", "G", "W"
		);

		$this->assertSame($expected, $partialA->extractLabelsToArray());

		$partialB = $mixer->setOperations(array(
			new Celsus_Mixer_Operation_Select_Simple(array(
				'count' => 5
			))
		))->process();

		$expected = array(
			"U", "W", "A", "B", "C"
		);

		$this->assertSame($expected, $partialB->extractLabelsToArray());

		// Now, interleave the results from the previous two mixes.
		$results = $mixer->setSources(array(
			$partialA,
			$partialB
		))->setOperations(array(
			new Celsus_Mixer_Operation_RoundRobin_ByPreviousOperation(array(
				'count' => 10,
				'steps' => array(
					$partialB->getType() => 2
				)
			)),
		))->process();

		$expected = array(
			"C", "U", "W", "F", "A", "B", "I", "E", "H", "D"
		);

		$this->assertSame($expected, $results->extractLabelsToArray());
	}
}