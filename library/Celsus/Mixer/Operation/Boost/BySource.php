<?php

/**
 * Boosting multiplies the confidence of each result by the specified factor.
 *
 * Results that come from multiple sources may have multiple boost factors applied.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Boost_BySource extends Celsus_Mixer_Operation {

	protected $_defaultConfig = array(
		'boost' => array()
	);

	protected $_name = 'boostBySource';

	protected function _process(Celsus_Mixer_Component_Group $results) {

		$return = new Celsus_Mixer_Component_Group();
		$processedItems = array();

		foreach ($results as $result) {
			$boostFactor = 1;
			foreach ($result->sources as $source) {
				if (isset($this->_config['boost'][$source])) {
					$boostFactor *= $this->_config['boost'][$source];
				}
			}
			$result->confidence *= $boostFactor;
		}

		return $results;
	}

}