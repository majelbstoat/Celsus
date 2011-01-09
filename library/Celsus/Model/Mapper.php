<?php

class Celsus_Model_Mapper {

	const MAPPER_TYPE_SIMPLE = 'Simple';

	const MAPPER_TYPE_COMPLEX = 'Complex';

	const MAPPER_TYPE_DISTRIBUTED = 'Distributed';

	const MAPPER_TYPE_CUSTOM = 'Custom';

	/**
	 * The service requiring access to the underlying.
	 */
	protected $_service = null;

	/**
	 * Whether or not to cache the next call.
	 *
	 * @var boolean
	 */
	protected $_caching = false;

	/**
	 * Flag to wrap the next query in a single model.
	 *
	 * @var boolean
	 */
	protected $_single = false;


	/**
	 * Flag to wrap the next query in a model set.
	 *
	 * @var boolean
	 */
	protected $_multiple = false;

	/**
	 * Indicates the next function call is cacheable.
	 *
	 * @return Celsus_Model_Mapper
	 */
	public function cache() {
		$this->_caching = true;
		return $this;
	}

	/**
	 * Indicates the next function call will give a single result.
	 *
	 * @return Celsus_Model_Mapper
	 */
	public function single() {
		$this->_single = true;
		return $this;
	}

	/**
	 * Indicates the next function call will give multiple results.
	 *
	 * @return Celsus_Model_Mapper
	 */
	public function multiple() {
		$this->_multiple = true;
		return $this;
	}
}