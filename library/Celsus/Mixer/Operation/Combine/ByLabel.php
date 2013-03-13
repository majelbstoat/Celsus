<?php

/**
 * Allows for combination of items by taking the first occurrence of
 * a result and decorating with subsequent sources.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Combine_ByLabel extends Celsus_Mixer_Operation {

	protected $_name = 'combineByLabel';

	protected function _process(Celsus_Pipeline_Result_Interface $results) {

		$return = new Celsus_Mixer_Component_Group();
		$map = array();
		$i = 0;

		foreach ($results as $i => $result) {
			if (isset($map[$result->label])) {
				$item = $return[$map[$result->label]];
				$item->sources = array_merge($item->sources, $result->sources);
			} else {
				$return[$i] = $result;
				$map[$result->label] = $i++;
			}
		}

		return $return;
	}
}