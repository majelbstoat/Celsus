<?php

/**
 * The backfill strategy attempts to guarantee a minimum number of components in the returned group.
 *
 * It does so by using one or more sources to yield more results until the minimum is reached.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Backfill extends Celsus_Mixer_Operation {

	protected $_defaultConfig = array(
		'minimum' => null,
		'sources' => array(),
		'tries'   => 1
	);

	protected $_name = 'backfill';

	protected function _process(Celsus_Pipeline_Result_Interface $results) {

		if (!$this->_config['minimum'] || !$this->_config['sources']) {
			throw new Celsus_Exception("A minimum and at least one source are required to backfill", Celsus_Http::INTERNAL_SERVER_ERROR);
		}

		for ($i = 0; $i < $this->_config['tries']; $i++) {
			foreach ($this->_config['sources'] as $source) {
				if (count($results) >= $this->_config['minimum']) {
					break 2;
				}

				// Let the source know that we already have some results and as it is
				// backfilling, we don't want to see those again.
				$results->append($source->yield(array(
					'exclude' => $results
				)));
			}
		}

		return $results->slice($this->_config['minimum']);
	}

}