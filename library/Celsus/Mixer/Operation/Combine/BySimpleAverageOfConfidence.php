<?php

/**
 * Allows for combination of items by summed confidence.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Combine_BySimpleAverageOfConfidence extends Celsus_Mixer_Operation {

	protected $_name = 'combineBySimpleAverageOfConfidence';

	protected function _process(Celsus_Pipeline_Result_Interface $results) {

		$return = new Celsus_Mixer_Component_Group();
		$iterations = array();
		$map = array();
		$i = 0;

		foreach ($results as $i => $result) {
			if (isset($map[$result->label])) {
				// Sum the confidence
				$iterations[$result->label]++;
				$item = $return[$map[$result->label]];
				$item->confidence = (($result->confidence - $item->confidence) / $iterations[$result->label]) + $item->confidence;
				$item->sources = array_merge($item->sources, $result->sources);
			} else {
				$return[$i] = $result;
				$map[$result->label] = $i++;
				$iterations[$result->label] = 1;
			}
		}

		return $return;
	}
}