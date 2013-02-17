<?php

/**
 * The round robin strategy takes one from each source in turn, until the required number is selected.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_CombinationStrategy_RoundRobin implements Celsus_Mixer_CombinationStrategy_Interface {

	public function combine($results, $count) {

		$returnCount = 0;
		$return = array();

		while ($results) {
			foreach ($results as $source => & $sourceResults) {
				$result = array_shift($sourceResults);

				if (!isset($return[$result->label])) {
					$return[$result->label] = $result;
					$returnCount++;
				}

				if ($returnCount == $count) {
					break 2;
				}

				if (!$sourceResults) {
					unset($results[$source]);
				}
			}
		}

		return $return;
	}

}