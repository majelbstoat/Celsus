<?php

/**
 * The simple strategy takes as many values as it can from each source in turn, until the required number is selected.
 *
 * The results will be deduplicated as a side-effect.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Select_Simple extends Celsus_Mixer_Operation {

	protected $_defaultConfig = array(
		'count' => null
	);

	protected $_name = 'selectSimple';

	protected function _process(Celsus_Mixer_Component_Group $results) {

		$returnCount = 0;
		$return = new Celsus_Mixer_Component_Group();
		$processedItems = array();

		foreach ($results as $result) {
			if (!isset($processedItems[$result->label])) {
				$return[] = $result;
				$processedItems[$result->label] = true;
				$returnCount++;
			}

			if ($this->_config['count'] && ($returnCount === $this->_config['count'])) {
				break;
			}
		}

		return $return;
	}

}