<?php

/**
 * This operation orders the items in the list by confidence.
 *
 * The sort is descending and stable, and the results are not-deduplicated.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Sort_ByRawVotes extends Celsus_Mixer_Operation_Sort_ByInteger {

	protected $_name = 'sortByRawVotes';

	public function decorate($results) {
		$decorated = array();

		foreach ($results as $i => $result) {
			if (isset($decorated[$result->label])) {
				$decorated[$result->label][1]++;
			} else {
				$decorated[$result->label] = array($i, 1, $result);
			}
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