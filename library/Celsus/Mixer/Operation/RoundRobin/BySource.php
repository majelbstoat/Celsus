<?php

/**
 * The round robin operation takes one result from each source in turn, until the required number is selected.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_RoundRobin_BySource extends Celsus_Mixer_Operation {

	protected $_defaultConfig = array(
		'count' => null,
		'steps' => array()
	);

	protected $_keyField = 'sources';

	protected $_name = 'roundRobinBySource';

	/**
	 * @see Celsus_Mixer_Operation_Interface::process()
	 */
	protected function _process(Celsus_Mixer_Component_Group $results) {

		$returnCount = 0;
		$return = new Celsus_Mixer_Component_Group();
		$processedItems = array();

		// First, break the monolithic results out by source.
		$splitResults = $this->_separateByField($results, $this->_keyField);

		$steps = $this->_generateSteps($splitResults);

		while ($splitResults) {
			foreach ($splitResults as $source => & $sourceResults) {
				for ($i = 0; $i < $steps[$source]; $i++) {
					$result = array_shift($sourceResults);

					if (!isset($processedItems[$result->label])) {
						$return[] = $result;
						$processedItems[$result->label] = true;
						$returnCount++;
					}

					if ($this->_config['count'] && ($returnCount === $this->_config['count'])) {
						break 3;
					}

					if (!$sourceResults) {
						unset($splitResults[$source]);
						break;
					}
				}
			}
		}

		return $return;
	}

	protected function _generateSteps($splitResults) {
		$steps = array();
		foreach (array_keys($splitResults) as $source) {
			$count = isset($this->_config['steps'][$source]) ? $this->_config['steps'][$source] : 1;
			$steps[$source] = $count;
		}
		return $steps;
	}
}