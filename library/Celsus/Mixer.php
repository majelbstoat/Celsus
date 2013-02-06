<?php

class Celsus_Mixer {

	protected $_sourceParent;

	public function __construct(Celsus_Mixer_Source_Interface $sourceParent) {
		$this->_sourceParent = $sourceParent;
	}

	public function get($count) {

	}

	// Weighing - from the sources themselves.

	// Boosting - according to mixing strategy.

	// Deduplicating - mixer.

	// Ranking - mixer

	// Selecting - according to mixing strategy

	// Diversity - according to diversity strategy

	// Backfilling - from designated backfill source.

	// Sampling - from designated sample source

}