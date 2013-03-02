<?php

/**
 * This operation orders the items in the list by confidence.
 *
 * The sort is descending and stable, and the results are not-deduplicated.
 *
 * @author majelbstoat
 */
abstract class Celsus_Mixer_Operation_Sort_ByString extends Celsus_Mixer_Operation_Sort {

	protected $_defaultConfig = array(
		'count' => null,
		'direction' => SORT_ASC,
		'caseInsensitive' => true
	);

	public function compare(array $a, array $b) {

		if (SORT_DESC == $this->_config['direction']) {
			$first = self::SORT_HIGHER;
			$second = self::SORT_LOWER;
		} else {
			$first = self::SORT_LOWER;
			$second = self::SORT_HIGHER;
		}

		$comparison = $this->_config['caseInsensitive'] ? strnatcasecmp($a[1], $b[1]) : strnatcmp($a[1], $b[1]);

		if (!$comparison) {
			// If the indexes are the same, prefer the first one (stable).
			return $a[0] < $b[0] ? -1 : 1;
		} else {
			return ($comparison < 0) ? $first : $second;
		}
	}
}