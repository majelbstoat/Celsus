<?php

abstract class Celsus_Mixer_Operation_Sort extends Celsus_Mixer_Operation {

	protected $_count = null;

	public function __construct($count = null) {
		$this->_count = $count;
	}

	/**
	 * Sorts the results according to specific criteria.
	 *
	 * PHP sort methods are not stable, so we allow specific methods to
	 * do decorate/sort/undecorate if they want.  Also allows expensive
	 * compare key operations to be processed once.
	 *
	 * @see Celsus_Mixer_Operation_Interface::process()
	 */
	public function process($results) {

		$decorated = $this->_decorate($results);

		uasort($decorated, array($this, '_compare'));

		$return = $this->_undecorate($decorated);

		if (null !== $this->_count) {
			$return = array_slice($return, 0, $this->_count);
		}

		return $return;
	}

	protected function _decorate($results) {
		return $results;
	}

	protected function _undecorate($decorated) {
		return $decorated;
	}

	abstract protected function _compare(array $a, array $b);
}