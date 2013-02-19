<?php

abstract class Celsus_Mixer_Operation_Sort extends Celsus_Mixer_Operation {

	protected $_count = null;

	public function __construct($count = null) {
		$this->_count = $count;
	}

	public function process($results) {
		$return = array();
		$decorated = array();

		// Need to do a pseudo-Schwartzian Transform as PHP sort methods are not stable.
		foreach ($results as $i => $result) {
			$decorated[] = array($i, $result);
		}

		uasort($decorated, array($this, '_compare'));

		// Undecorate.
		foreach ($decorated as $decoratedItem) {
			$return[] = $decoratedItem[1];
		}

		if (null !== $this->_count) {
			$return = array_slice($return, 0, $this->_count);
		}

		return $return;
	}

	abstract protected function _compare(array $a, array $b);
}