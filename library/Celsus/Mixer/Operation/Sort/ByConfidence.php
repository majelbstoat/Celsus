<?php

/**
 * This operation orders the items in the list by confidence.
 *
 * The sort is descending and stable, and the results are not-deduplicated.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Sort_ByConfidence extends Celsus_Mixer_Operation_Sort_ByInteger {

	protected $_name = 'sortByConfidence';

	public function decorate($results) {
		$decorated = array();

		foreach ($results as $i => $result) {
			$decorated[] = array($i, $result->confidence, $result);
		}

		return $decorated;
	}

	public function undecorate($decorated) {
		$results = array();

		foreach ($decorated as $decoratedItem) {
			$results[] = $decoratedItem[2];
		}

		return $results;
	}
}

