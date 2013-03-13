<?php

abstract class Celsus_Mixer_Operation_Sort extends Celsus_Mixer_Operation {

	const SORT_HIGHER = 1;
	const SORT_LOWER = -1;

	protected $_defaultConfig = array(
		'count' => null,
		'direction' => SORT_DESC
	);

	/**
	 * Sorts the results according to specific criteria.
	 *
	 * PHP sort methods are not stable, so we allow specific methods to
	 * do decorate/sort/undecorate if they want.  Also allows expensive
	 * compare key operations to be processed once.
	 *
	 * @see Celsus_Mixer_Operation_Interface::process()
	 */
	protected function _process(Celsus_Pipeline_Result_Interface $results) {

		$results->sort(array($this, 'compare'), array($this, 'decorate'), array($this, 'undecorate'));

		if ($this->_config['count']) {
			$results = $results->slice($this->_config['count']);
		}

		return $results;
	}

	public function decorate($results) {
		return $results;
	}

	/**
	 * @param array $decorated
	 */
	public function undecorate($decorated) {
		return $decorated;
	}

	abstract public function compare(array $a, array $b);
}