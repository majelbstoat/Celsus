<?php

/**
 * The backfill strategy attempts to guarantee a minimum number of components in the returned group.
 *
 * It does so by using one or more sources to yield more results until the minimum is reached.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Sample extends Celsus_Mixer_Operation {

	protected $_defaultConfig = array(
		'samplePercent' => null,
		'sources' => array(),
	);

	protected $_name = 'backfill';

	protected function _process(Celsus_Mixer_Component_Group $results) {

		if (!$this->_config['samplePercent'] || !$this->_config['sources']) {
			throw new Celsus_Exception("A sample percent and at least one source are required to sample content", Celsus_Http::INTERNAL_SERVER_ERROR);
		}

		if ($this->_config['samplePercent'] > 100) {
			throw new Celsus_Exception("Sample percent must be less than or equal to 100", Celsus_Http::INTERNAL_SERVER_ERROR);
		}

		$resultCount = count($results);
		$slice = (int) ceil(($this->_config['samplePercent'] / 100) * $resultCount);

		$sampled = null;
		foreach ($this->_config['sources'] as $source) {
			$yielded = $source->yield();
			$sampled = (null === $sampled) ? $yielded : $sampled->append($yielded);
			if (count($sampled) >= $slice) {
				$sampled = $sampled->slice($slice);
				break;
			}
		}

		return $results->slice($resultCount - $slice)->append($sampled);
	}

}