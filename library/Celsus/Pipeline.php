<?php

/**
 * A pipeline provides the capability of taking data from one or more
 * sources and performing a series sequential operations on them. The
 * flexible architecture is intended to allow for a variety of
 * different pipeline types.
 *
 * @author majelbstoat
 */
abstract class Celsus_Pipeline {

	const OPERATION_INSERTION_START = 0;
	const OPERATION_INSERTION_END = 1;

	protected $_inputData = null;

	protected $_operations = array();

	protected $_sources = array();

	protected $_sourceConfiguration = array();

	protected $_sourceTypes = null;

	protected $_sourceParent = null;

	public function __construct($sourceParent = null) {

		if (null !== $sourceParent) {
			$interfaces = class_implements($sourceParent);

			if (!in_array('Celsus_Pipeline_Source_Interface', $interfaces)) {
				throw new Celsus_Exception("$sourceParent must implement Celsus_Pipeline_Source_Interface", Celsus_Http::INTERNAL_SERVER_ERROR);
			}

			$this->_sourceParent = $sourceParent;
		}
	}

	public function process() {

		// Prepend any default operations if necessary.
		$defaultOperations = $this->_getDefaultOperations();
		if ($defaultOperations) {
			$this->addOperations($defaultOperations, self::OPERATION_INSERTION_START);
		}

		// First, see if we already have input data to work from.
		$results = $this->getInputData();

		if (null === $results) {
			// If not, we need to get the sources that we will be pulling from.
			$sources = $this->getSources();

			// Then query those sources for results.
			$results = $this->querySources($sources);
		}

		$operations = array();
		while ($operation = $this->_getNextOperation()) {
			$operations[] = $operation->getName();
			$results = $operation->setPipeline($this)->process($results);
		}

		return $this->_complete($results, $operations);
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

	public function getInputData() {
		return $this->_inputData;
	}

	public function setInputData(Celsus_Pipeline_Result_Interface $inputData) {
		$this->_inputData = $inputData;

		return $this;
	}

	public function configureSource($sourceType, $config) {
		$this->_sourceConfiguration[$sourceType] = $config;

		return $this;
	}

	public function addOperation(Celsus_Pipeline_Operation_Interface $operation, $position = self::OPERATION_INSERTION_END) {
		$operation->setPipeline($this);
		if (self::OPERATION_INSERTION_END === $position) {
			$this->_operations[] = $operation;
		} else {
			array_unshift($this->_operations, $operation);
		}
		return $this;
	}

	public function addOperations(array $operations, $position = self::OPERATION_INSERTION_END) {
		foreach ($operations as $operation) {
			$this->addOperation($operation, $position);
		}

		return $this;
	}

	public function setOperations(array $operations) {
		$this->_operations = $operations;
		return $this;
	}

	public function setSources($sources) {
		$this->clearSources();
		foreach ($sources as $source) {
			$this->addSource($source);
		}
		return $this;
	}

	public function addSource(Celsus_Pipeline_Source_Interface $source) {
		$this->_sources[$source->getType()] = $source;

		return $this;
	}

	public function clearSources() {
		$this->_sources = array();

		return $this;
	}

	protected function _complete($results, $operations) {
		return $results;
	}

	protected function _getDefaultOperations() {}

	protected function _getNextOperation() {
		return array_shift($this->_operations);
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
	protected function _hash($operations) {
		$data = array_merge(array_keys($this->getSources()), $operations);

		return substr(sha1(implode(":", $data)), 0, 7);
	}

	abstract protected function querySources($sources);
}