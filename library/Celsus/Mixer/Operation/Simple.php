<?php

/**
 * The simple strategy takes as many values as it can from each source in turn, until the required number is selected.
 *
 * The results will be deduplicated as a side-effect.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Simple extends Celsus_Mixer_Operation {

	protected $_count;

	public function __construct($count) {
		$this->_count = $count;
	}

	public function process($results) {

		$returnCount = 0;
		$return = array();
		$processedItems = array();

		foreach ($results as $result) {
			if (!isset($processedItems[$result->label])) {
				$return[] = $result;
				$processedItems[$result->label] = true;
				$returnCount++;
			}

			if ($returnCount == $this->_count) {
				break;
			}
		}

		return $return;
	}

}