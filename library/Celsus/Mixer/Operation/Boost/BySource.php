<?php

/**
 * The simple strategy takes as many values as it can from each source in turn, until the required number is selected.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Boost_BySource extends Celsus_Mixer_Operation {

	protected $_defaultConfig = array(
		'boost' => array()
	);

	protected $_name = 'boostBySource';

	protected function _process(Celsus_Mixer_Component_Group $results) {

		$returnCount = 0;
		$return = new Celsus_Mixer_Component_Group();
		$processedItems = array();

		foreach ($results as $result) {
			$boostFactor = isset($this->_config['boost'][$result->source]) ? $this->_config['boost'][$result->source] : 1.0;
			$result->confidence *= $boostFactor;
		}

		return $results;
	}

}