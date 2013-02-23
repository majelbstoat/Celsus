<?php

/**
 * This operation orders the items in the list by confidence.
 *
 * The sort is descending and stable, and the results are not-deduplicated.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Sort_ByConfidence extends Celsus_Mixer_Operation_Sort {

	protected function _compare(array $a, array $b) {

		if ($a[1] === $b[1]) {
			// If the indexes are the same, prefer the first one (stable).
			return $a[0] < $b[0] ? -1 : 1;
		} else {
			// Otherwise, higher confidences should sort first (left, hence -1)
			return ($a[1] < $b[1]) ? 1 : -1;
		}
	}

	protected function _decorate($results) {
		$decorated = array();

		foreach ($results as $i => $result) {
			$decorated[] = array($i, $result->confidence, $result);
		}

		return $decorated;
	}

	protected function _undecorate($decorated) {
		$results = array();

		foreach ($decorated as $decoratedItem) {
			$results[] = $decoratedItem[2];
		}

		return $results;
	}
}