<?php

class Celsus_Test_Mixer_Source_Result {

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
	public static function generateSimpleResultSet($sourceDefinition, $initialConfidence = 100, $confidenceStep = 1) {

		$return = array();
		foreach ($sourceDefinition as $sourceName => $desiredResults) {
			$confidence = $initialConfidence;
			foreach ($desiredResults as $desiredResult) {
				$return[] = new Celsus_Mixer_Source_Result(array(
					'confidence' => $confidence,
					'label' => $desiredResult,
					'result' => null,
					'source' => $sourceName
				));
				$confidence -= $confidenceStep;
			}
		}

		return $return;
	}

	/**
	 * Counts the total number of sources in a source result set, regardless of duplicates.
	 *
	 * @param Celsus_Mixer_SourceResult[] $sourceData
	 * @return mixed|number
	 */
	public static function countSources($sourceData) {
		return array_reduce($sourceData, function($partial, $item) {
			return $partial + count($item);
		}, 0);
	}

	public static function extractLabelsToArray($results) {
		$return = array();
		foreach ($results as $result) {
			$return[] = $result->label;
		}
		return $return;
	}
}