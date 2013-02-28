<?php

/**
 * This operation orders the items in the list by simple average confidence.
 *
 * The sort is stable, and by default is descending.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Sort_BySimpleAverageOfConfidence extends Celsus_Mixer_Operation_Sort_ByConfidence {

	protected $_name = 'sortBySimpleAverageOfConfidence';

	public function decorate($results) {
		$decorated = array();
		$iterations = array();

		// @todo Prefer the item that has the same average, but more sources?
		foreach ($results as $i => $result) {
			if (isset($decorated[$result->label])) {
				// Incremental Average
				$iterations[$result->label]++;
				$decorated[$result->label][1] = (($result->confidence - $decorated[$result->label][1]) / $iterations[$result->label]) + $decorated[$result->label][1];
			} else {
				$decorated[$result->label] = array($i, $result->confidence, $result);
				$iterations[$result->label] = 1;
			}
		}

		return $decorated;
	}
}