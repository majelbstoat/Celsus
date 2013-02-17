<?php

/**
 * The simple strategy takes as many values as it can from each source in turn, until the required number is selected.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_CombinationStrategy_Simple implements Celsus_Mixer_CombinationStrategy_Interface {

	public function combine($results, $count) {

		$returnCount = 0;
		$return = array();

		foreach ($results as $source => $resultSet) {
			foreach ($resultSet as $result) {
				if (!isset($return[$result->label])) {
					$return[$result->label] = $result;
					$returnCount++;
				}

				if ($returnCount == $count) {
					break 2;
				}
			}
		}

		return $return;
	}

}