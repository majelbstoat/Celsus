<?php

class Celsus_Model_Base_Redis extends Celsus_Model_Base {

	const INDEX_TYPE_SIMPLE_HASH = 'simpleHash';
	const INDEX_TYPE_SET_MEMBERS = 'setMembers';
	const INDEX_TYPE_SORTED_SET_MEMBERS = 'sortedSetMembers';
	const INDEX_TYPE_SORTED_SET_LOOKUP = 'sortedSetLookup';

	/**
	 * The adapter to use for this connection.
	 *
	 * @var Celsus_Db_Document_Adapter_Redis
	 */
	protected $_adapter;

	/**
	 * The adapter to use if it hasn't been set.
	 *
	 * @var unknown_type
	 */
	protected static $_defaultAdapter = null;

	protected static $_dataClass = 'Celsus_Db_Document_Redis';

	public function __construct(array $config = array()) {
		$this->_adapter = isset($config['adapter']) ? $config['adapter'] : self::getDefaultAdapter();
	}

	/**
	 * Deletes from permanent storage, based on the supplied query.
	 * @param array|string $where
	 */
	public function delete($where) {

		$identifier = $where['identifier'];
		$data = $where['data'];
		$originalData = $where['originalData'];
		$metadata = $where['metadata'];

		$adapter = $this->getAdapter();
		$pipeline = $adapter->startPipeline();

		$this->updateIndices($identifier, $data, $originalData, $metadata, $pipeline);

		$pipeline->sRem($metadata['_type'], $identifier)
			->delete($identifier);

		$adapter->send($pipeline);
	}

	protected function _getDefaults() {
		$fields = $this->getFields();
		$defaults = array_combine($fields, array_fill(0, count($fields), null));
		$defaults['_type'] = $this->_name;
		$defaults['_created'] = null;

		return $defaults;
	}

	/**
	 * Returns the fields that represent this model in the underlying redis database.
	 *
	 * @return array
	 */
	public function getFields() {
		return array_merge(array('id'), static::$_fields, array('_type', '_created'));
	}

	/**
	 * Filters a set of items by the specified parameters.
	 *
	 * @param array $parameters
	 * @return Celsus_Db_Document_Set_Redis|null
	 */
	public function rangeFilter($parameters) {
		$parameters['group'] = $this->_name;

		$type = (isset($parameters['startScore']) || isset($parameters['endScore']))
		? Celsus_Db_Document_Redis_Query::QUERY_TYPE_SORTED_SET_SCORE
		: Celsus_Db_Document_Redis_Query::QUERY_TYPE_SORTED_SET_RANGE;

		$query = new Celsus_Db_Document_Redis_Query(array(
			'indexType' => $type,
			'parameters' => $parameters
		));

		return $this->fetchAll($query);
	}

	/**
	 * Passes a query to the database adapter.
	 *
	 * @param Celsus_Db_Document_Redis_Query
	 * @return Celsus_Db_Document_Set_Redis|null
	 */
	public function fetchAll() {
		$query = func_get_arg(0);
		return $this->getAdapter()->query($query);
	}

	/**
	 * Finds records based on identifiers.
	 *
	 * @param array|string $identifiers.  The identifier of the record to find or a query or view to get one.
	 * @return Celsus_Db_Document_Set_Redis|null
	 */
	public function find() {
		$arguments = func_get_args();
		$identifiers = $arguments[0];

		if (!is_array($identifiers)) {
			$identifiers = array($identifiers);
		}

		// Fetch records by the identifiers.
		$results = $this->getAdapter()->find($identifiers);

		// If we found records, make sure they're all of the right type.
		return $results ? $this->_filterResultSet($results) : null;
	}

	/**
	 * Ensures that all the documents in the result set are of the right type.
	 *
	 * Any documents that aren't of the correct type will be removed from the set.
	 *
	 * @param Celsus_Db_Document_Set_Redis $results
	 * @return Celsus_Db_Document_Set_Redis
	 */
	protected function _filterResultSet(Celsus_Db_Document_Set_Redis $results) {

		$invalidIds = array();
		foreach ($results as $id => $result) {
			if ($result->_type !== $this->_name) {
				$invalidIds[] = $id;
			}
		}

		// If any of the items were of different types, we need to remove them.
		if ($invalidIds) {
			$results->remove($invalidIds);
		}

		return $results;
	}

	/**
	 * Gets the adapter for this base.
	 *
	 * @return Celsus_Db_Document_Adapter_Redis
	 */
	public function getAdapter() {
		return $this->_adapter;
	}

	/**
	 * Updates the secondary indices for this model representation to aid queries.
	 *
	 * @see Celsus_Model_Base::updateIndices()
	 */
	public function updateIndices($id, $data, $originalData, $metadata, Redis $pipeline = null) {
		$indices = $this->getIndices();
		if ($indices) {
			$adapter = $this->getAdapter();
			$pipelined = true;

			// If we don't already have a pipeline, create one.
			if (null === $pipeline) {
				$pipelined = false;
				$pipeline = $adapter->startPipeline();
			}

			$config = array(
				'group' => $this->_name,
				'new' => $data,
				'old' => $originalData,
				'metadata' => $metadata,
			);

			// Queue all the index updates.
			foreach ($indices as $index) {

				$config['field'] = $index['field'];
				if (isset($index['parameters'])) {
					$config['parameters'] = $index['parameters'];
				} else {
					unset($config['parameters']);
				}

				$adapter->updateIndex($index['type'], $id, $config, $pipeline);
			}

			// Send the pipeline if we were responsible for creating it.
			if (!$pipelined) {
				$adapter->send($pipeline);
			}
		}
	}

}