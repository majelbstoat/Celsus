<?php

class Celsus_Test_Mixer_Component {

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
	 * @return Celsus_Mixer_Component_Group
	 */
	public static function generateSimpleComponentGroup($sourceDefinition, $initialConfidence = 100, $confidenceStep = 1) {

		$return = array();
		foreach ($sourceDefinition as $sourceName => $desiredResults) {
			$confidence = $initialConfidence;
			foreach ($desiredResults as $desiredResult) {
				$return[] = new Celsus_Mixer_Component(array(
					'confidence' => $confidence,
					'label' => $desiredResult,
					'result' => null,
					'sources' => array($sourceName)
				));
				$confidence -= $confidenceStep;
			}
		}

		return new Celsus_Mixer_Component_Group($return);
	}

	/**
	 * Counts the total number of sources in a component group, regardless of duplicates.
	 *
	 * @param Celsus_Mixer_Component_Group[] $sourceData
	 * @return int
	 */
	public static function countSources($sourceData) {
		$count = 0;

		foreach ($sourceData as $item) {
			$count += count($item);
		}

		return $count;
	}
}