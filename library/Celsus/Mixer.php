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
class Celsus_Mixer {

	protected $_operators = array();

	protected $_sources = array();

	protected $_sourceConfiguration = array();

	protected $_sourceParent = null;

	protected $_sourceTypes = null;

	public function __construct($sourceParent) {
		$interfaces = class_implements($sourceParent);

		if (!in_array('Celsus_Mixer_Source_Interface', $interfaces)) {
			throw new Celsus_Exception("$sourceParent must implement Celsus_Mixer_Source_Interface", Celsus_Http::INTERNAL_SERVER_ERROR);
		}
		$this->_sourceParent = $sourceParent;
	}

	public function setSourceTypes(array $sourceTypes) {
		$sourceParent = $this->_sourceParent;
		$availableSourceTypes = $sourceParent::getTypes();
		$sourceTypeMap = array_flip($availableSourceTypes);

		foreach ($sourceTypes as $sourceType) {
			if (!isset($sourceTypeMap[$sourceType])) {
				$class = get_class($this->_sourceParent);
				throw new Celsus_Exception("$sourceType is not a valid source type for $class", Celsus_Http::INTERNAL_SERVER_ERROR);
			}
		}

		$this->_sourceTypes = $sourceTypes;
	}

	public function getSourceTypes() {
		if (null === $this->_sourceTypes) {
			$sourceParent = $this->_sourceParent;
			$sourceTypes = $sourceParent::getTypes();
			$this->_sourceTypes = array_combine($sourceTypes, $sourceTypes);
		}
		return $this->_sourceTypes;
	}

	public function setSources($sources) {
		$this->clearSources();
		foreach ($sources as $source) {
			$this->addSource($source);
		}
		return $this;
	}

	public function addSource(Celsus_Mixer_Source_Interface $source) {
		$sourceTypes = $this->getSourceTypes();
		$class = 'Celsus_Mixer_Component_Group';
		if (!($source instanceof $this->_sourceParent) && !($source instanceof $class)) {
			throw new Celsus_Exception("$source is not a valid source", Celsus_Http::INTERNAL_SERVER_ERROR);
		}

		$this->_sources[$source->getType()] = $source;

		return $this;
	}

	public function clearSources() {
		$this->_sources = array();

		return $this;
	}

	public function getSources() {
		if (!$this->_sources) {
			// We haven't directly specified any sources, so load all of them specified on the source parent.
			$sourceTypes = $this->getSourceTypes();

			$sourceParent = $this->_sourceParent;
			foreach ($sourceTypes as $sourceType) {
				$config = isset($this->_sourceConfiguration[$sourceType]) ? $this->_sourceConfiguration[$sourceType] : array();
				$source = $sourceParent::getSource($sourceType, $config);
				$this->_sources[$sourceType] = $source;
			}
		}

		return $this->_sources;
	}

	public function configureSource($sourceType, $config) {
		$this->_sourceConfiguration[$sourceType] = $config;

		return $this;
	}

	public function addOperator($operator) {
		$this->_operators[] = $operator;
		return $this;
	}

	public function addOperators(array $operators) {
		$this->_operators = array_merge($this->_operators, $operators);
		return $this;
	}

	public function setOperators(array $operators) {
		$this->_operators = $operators;
		return $this;
	}

	public function mix() {

		// First, get the sources that we will be pulling from.
		$sources = $this->getSources();

		$results = new Celsus_Mixer_Component_Group();

		foreach ($sources as $source) {
			$results = $results->append($source->yield());
		}

		foreach ($this->_operators as $operator) {
			$results = $operator->process($results);
		}

		$hash = $this->_hash();

		$results->setType($hash)
			->noteOperation($hash);

		return $results;
	}

	/**
	 * Computes a reasonably unique 7 byte hash of the parameters used for mixing.
	 *
	 * This is not for security purposes, but to allow the results of mixing
	 * to be used as the input to a secondary mix, without colliding with
	 * another pre-mixed input.
	 *
	 * @return string
	 */
	protected function _hash() {
		$data = array_keys($this->getSources());
		foreach ($this->_operators as $operator) {
			$data[] = get_class($operator);
		}
		return substr(sha1(implode(":", $data)), 0, 7);
	}
}

/**
 * Combination strategies:
 *
 * Decorate Only: Take all the results from source A, replace the source as source B for all items in B that are in A. Repeat for C.
 *
 *	// + Boosting
	// + Deduplicating
	// + Ranking
	// + Combination
	//   Diversity
	// + Backfilling
	// + Sampling
*/
