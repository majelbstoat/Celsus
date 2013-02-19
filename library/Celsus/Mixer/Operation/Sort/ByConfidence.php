<?php

/**
 * This operation orders the items in the list by confidence.
 *
 * The sort is descending and the results are not-deduplicated.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Sort_ByConfidence extends Celsus_Mixer_Operation_Sort {

	protected function _compare(array $a, array $b) {

		if ($a[1]->confidence === $b[1]->confidence) {
			return $a[0] < $b[0] ? -1 : 1;
		} else {
			return ($a[1]->confidence < $b[1]->confidence) ? 1 : -1;
		}
	}
}