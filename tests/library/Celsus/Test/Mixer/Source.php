<?php

abstract class Celsus_Test_Mixer_Source extends Celsus_Mixer_Source {

	const SOURCE_TYPE_A = 'A';
	const SOURCE_TYPE_B = 'B';
	const SOURCE_TYPE_C = 'C';

	protected static $_types = array(
		self::SOURCE_TYPE_A,
		self::SOURCE_TYPE_B,
		self::SOURCE_TYPE_C
	);

	protected $_desiredResults = array();

	public static function getSource($type) {
		$classname = get_called_class() . '_' . $type;
		return new $classname();
	}

	public function setDesiredResults(array $desiredResults) {
		$this->_desiredResults = $desiredResults;
	}

	public function yield($maximum) {
		return array_slice($this->_desiredResults, 0, $maximum);
	}

	/**
	 * Helper method to help generate dummy source result sets from simple definitions.
	 *
	 * Takes an array of arrays like:
	 *
	 * $sourceDefinition = array(
	 * 	"A" => array("A", "B"),
	 *	"B" => array("C", "D", "A", "B"),
	 *  "C" => array("G", "H", "I", "J")
	 * );
	 *
	 * @param array $sourceDefinition
	 */
	public static function generateSimpleSourceSet($sourceDefinition, $initialConfidence = 100, $confidenceStep = 1) {

		$sources = array();
		foreach ($sourceDefinition as $sourceType => $desiredResults) {
			$confidence = $initialConfidence;
			$results = array();
			$source = self::getSource($sourceType);
			foreach ($desiredResults as $desiredResult) {
				$results[] = new Celsus_Mixer_Source_Result(array(
					'confidence' => $confidence,
					'label' => $desiredResult,
					'result' => null,
					'source' => $sourceType
				));
				$confidence -= $confidenceStep;
			}
			$source->setDesiredResults($results);
			$sources[] = $source;
		}

		return $sources;
	}
}