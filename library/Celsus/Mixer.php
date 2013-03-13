<?php

/**
 * Used to combine results from multiple deterministic and non-deterministic data sources
 * based on various mixing strategies, with support for diversity filtering, sampling
 * and boosting.
 *
 * Intended to be a general purpose mixer than can be parameterised by its source context.
 *
 * @author majelbstoat
 */
class Celsus_Mixer extends Celsus_Pipeline {

	protected function querySources($sources) {

		$results = new Celsus_Mixer_Component_Group();

		foreach ($sources as $source) {
			$results = $results->append($source->yield());
		}

		return $results;
	}

	protected function _complete($results, $operations) {
		$hash = $this->_hash($operations);

		$results->setType($hash)
			->noteOperation($hash);

		return $results;
	}
}