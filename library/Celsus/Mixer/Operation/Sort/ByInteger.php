<?php

/**
 * This operation orders the items in the list by confidence.
 *
 * The sort is descending and stable, and the results are not-deduplicated.
 *
 * @author majelbstoat
 */
abstract class Celsus_Mixer_Operation_Sort_ByInteger extends Celsus_Mixer_Operation_Sort {

	public function compare(array $a, array $b) {

		if (SORT_DESC == $this->_config['direction']) {
			$first = self::SORT_HIGHER;
			$second = self::SORT_LOWER;
		} else {
			$first = self::SORT_LOWER;
			$second = self::SORT_HIGHER;
		}

		if ($a[1] === $b[1]) {
			// If the indexes are the same, prefer the first one (stable).
			return $a[0] < $b[0] ? -1 : 1;
		} else {
			return ($a[1] < $b[1]) ? $first : $second;
		}
	}
}