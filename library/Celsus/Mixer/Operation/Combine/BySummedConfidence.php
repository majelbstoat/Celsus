<?php

/**
 * Allows for combination of items by summed confidence.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Combine_BySummedConfidence extends Celsus_Mixer_Operation {

	protected $_name = 'combineBySummedConfidence';

	protected function _process(Celsus_Mixer_Component_Group $results) {

		$return = new Celsus_Mixer_Component_Group();
		$map = array();
		$i = 0;

		foreach ($results as $i => $result) {
			if (isset($map[$result->label])) {
				// Sum the confidence
				$item = $return[$map[$result->label]];
				$item->confidence += $result->confidence;
				$item->sources = array_merge($item->sources, $result->sources);
			} else {
				$return[$i] = $result;
				$map[$result->label] = $i++;
			}
		}

		return $return;
	}
}