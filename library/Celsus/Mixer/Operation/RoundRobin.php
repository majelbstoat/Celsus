<?php

/**
 * The round robin operation takes one result from each source in turn, until the required number is selected.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_RoundRobin extends Celsus_Mixer_Operation {

	protected $_count;

	public function __construct($count) {
		$this->_count = $count;
	}

	public function process($results) {

		$returnCount = 0;
		$return = array();
		$processedItems = array();

		// First, break the monolithic results out by source.
		$results = $this->_separateByField($results, 'source');

		while ($results) {
			foreach ($results as $source => & $sourceResults) {
				$result = array_shift($sourceResults);

				if (!isset($processedItems[$result->label])) {
					$return[] = $result;
					$processedItems[$result->label] = true;
					$returnCount++;
				}

				if ($returnCount == $this->_count) {
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