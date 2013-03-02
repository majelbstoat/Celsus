<?php

/**
 * This operation orders the items in the list by label, using natural ordering.
*
* The sort is descending and stable, and the results are not-deduplicated. Comparisons
* can be case insensitive or sensitive.
*
* @author majelbstoat
*/
class Celsus_Mixer_Operation_Sort_ByLabel extends Celsus_Mixer_Operation_Sort_ByString {

	protected $_name = 'sortByLabel';

	public function decorate($results) {
		$decorated = array();

		foreach ($results as $i => $result) {
			$decorated[] = array($i, $result->label, $result);
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