<?php

class Celsus_Db_Document_Adapter_Redis extends Celsus_Db_Document_Adapter {

	// Redis defines 16 numerically named databases.  By convention,
	// we reserve 0 for testing as it is the default, and 1 for development.
	const DATABASE_TESTING = 0;
	const DATABASE_DEVELOPMENT = 1;

	const KEY_OBJECT_COUNTER = "system:objectCounter";

	/**
	 * The client used to communicate with Redis.
	 *
	 * @var Redis $_client
	 */
	protected $_client = null;

	/**
	 * Default connection information.
	 *
	 * @var array
	 */
	protected $_config = array(
		'host' => '127.0.0.1',
		'port' => 6379,
		'timeout' => 2.5,
		'db' => self::DATABASE_TESTING
	);

	/**
	 * Whether the connection information has changed.
	 *
	 * @var boolean
	 */
	protected $_dirty = false;

	public function __construct($config = array()) {
		if (null !== $config) {
			if (is_array($config)) {
				$this->setFromArray($config);
			} elseif ($config instanceof Zend_Config) {
				$this->setFromConfig($config);
			}
		}
	}

	public function setFromArray(array $options) {
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	public function setFromConfig(Zend_Config $config) {
		return $this->setFromArray($config->toArray());
	}

	/**
	 * Set database host
	 *
	 * @param  string $host
	 * @return Celsus_Db_Document_Adapter_Couch
	 */
	public function setHost($host) {
		$this->_config['host'] = $host;
		$this->_dirty = true;
		return $this;
	}

	/**
	 * Retrieve database host
	 *
	 * @return string
	 */
	public function getHost() {
		return $this->_config['host'];
	}

	/**
	 * Set database host port
	 *
	 * @param  int $port
	 * @return Celsus_Db_Document_Adapter_Redis
	 */
	public function setPort($port) {
		$this->_config['port'] = (int) $port;
		$this->_dirty = true;
		return $this;
	}

	/**
	 * Retrieve database host port
	 *
	 * @return int
	 */
	public function getPort() {
		return $this->_config['port'];
	}

	/**
	 * Set database timeout
	 *
	 * @param float $timeout
	 * @return Celsus_Db_Document_Adapter_Redis
	 */
	public function setTimeout($timeout) {
		$this->_config['timeout'] = $timeout;
		return $this;
	}

	/**
	 * Retrieve database timeout.
	 *
	 * @return float
	 */
	public function getTimeout() {
		return $this->_config['timeout'];
	}

	/**
	 * @return Celsus_Db_Document_Adapter_Redis
	 */
	public function setDb($db) {
		$this->_config['db'] = $db;
		return $this;
	}

	public function getDb() {
		return $this->_config['db'];
	}

	/**
	 * Queries redis for the specified identifier or identifiers.
	 *
	 * @param string|array $identifiers
	 * @return Celsus_Db_Document_Set_Redis|null
	 */
	public function find($identifiers) {
		if (!is_array($identifiers)) {
			$identifiers = array($identifiers);
		}

		$client = $this->startPipeline();
		foreach ($identifiers as $identifier) {
			$client->hGetAll($identifier);
		}

		$results = $client->exec();

		$data = array();

		foreach ($identifiers as $identifier) {
			$result = array_shift($results);
			if ($result) {
				$data[$identifier] = $result;
			}
		}

		if ($data) {
			return new Celsus_Db_Document_Set_Redis(array(
				'adapter' => $this,
				'data' => $data
			));
		}

		return null;
	}

	/**
	 * Queries the database using secondary index.
	 *
	 * @param Celsus_Db_Document_Redis_Query $query
	 * @return Celsus_Db_Document_Set_Redis
	 */
	public function query(Celsus_Db_Document_Redis_Query $query) {
		$parameters = $query->getParameters();
		$indexType = $query->getIndexType();

		switch ($indexType) {
			case $query::QUERY_TYPE_HASH_ELEMENT:
				$identifiers = $this->getClient()->hGet($parameters['key'], $parameters['value']);
				break;

			case $query::QUERY_TYPE_SORTED_SET_RANGE:

				if (isset($parameters['auxiliary'])) {
					$sortFieldName = implode('', array_map('ucfirst', explode('_', $parameters['index'])));
					$lookupFieldName = implode('', array_map('ucfirst', explode('_', $parameters['auxiliary'])));

					$key = $parameters['group'] . ':sortedLookupBy' . $sortFieldName . $lookupFieldName . ':' . $parameters['auxiliaryId'];
				} else {
					$key = $parameters['group'] . ':sortedBy' . implode('', array_map('ucfirst', explode('_', $parameters['index'])));
				}

				$reversed = isset($parameters['reversed']) ? $parameters['reversed'] : false;
				$start = isset($parameters['offset']) ? $parameters['offset'] : 0;
				$end = isset($parameters['limit']) ? $start - 1 + $parameters['limit'] : -1;

				if ($reversed) {
					$identifiers = $this->getClient()->zRevRange($key, $start, $end);
				} else {
					$identifiers = $this->getClient()->zRange($key, $start, $end);
				}
				break;

			case $query::QUERY_TYPE_SORTED_SET_SCORE:

				if (isset($parameters['auxiliary'])) {
					$sortFieldName = implode('', array_map('ucfirst', explode('_', $parameters['index'])));
					$lookupFieldName = implode('', array_map('ucfirst', explode('_', $parameters['auxiliary'])));

					$key = $parameters['group'] . ':sortedLookupBy' . $sortFieldName . $lookupFieldName . ':' . $parameters['auxiliaryId'];
				} else {
					$key = $parameters['group'] . ':sortedBy' . implode('', array_map('ucfirst', explode('_', $parameters['index'])));
				}

				// Determine the start score.
				if (isset($parameters['startScore'])) {
					$start = $parameters['startScore'];
					if (isset($parameters['startExcluded'])) {
						$start = "($start";
					}
					$missingStart = false;
				} else {
					$start = '-inf';
					$missingStart = true;
				}

				// Determine the end score.
				if (isset($parameters['endScore'])) {
					$end = $parameters['endScore'];
					if (isset($parameters['endExcluded'])) {
						$end = "($end";
					}
					$missingEnd = false;
				} else {
					$end = '+inf';
					$missingEnd = true;
				}

				if ($parameters['limit']) {
					// Limit parameters need to be integers otherwise they aren't applied.
					$options['limit'] = array((int) $parameters['offset'], (int) $parameters['limit']);
				}

				if ($parameters['reversed']) {
					if ($missingEnd) {
						// H, F, G, following a previous set of E, D, C.
						$identifiers = array_reverse($this->getClient()->zRangeByScore($key, $start, $end, $options));
					} else {
						$identifiers = $this->getClient()->zRevRangeByScore($key, $end, $start, $options);
					}
				} else {
					if ($missingStart) {
						$identifiers = array_reverse($this->getClient()->zRevRangeByScore($key, $end, $start, $options));
					} else {
						$identifiers = $this->getClient()->zRangeByScore($key, $start, $end, $options);
					}
				}
				break;
		}

		return $identifiers ? $this->find($identifiers) : null;
	}

	/**
	 * Acquires a globally unique ID from a well known atomic object counter.
	 *
	 * @return int
	 */
	public function acquireId() {
		return $this->getClient()->incr(self::KEY_OBJECT_COUNTER);
	}

	public function cardinality($setName) {
		return $this->getClient()->sCard($setName);
	}

	public function save(Celsus_Db_Document_Redis $document)
	{
		$id = $document->getId();
		$data = $document->toArray();

		// Add the item to a sorted set, and set the modified fields.
		$result = $this->startPipeline()
			->sAdd($data['_type'], $id)
			->hMset($id, $data)
			->exec();
	}

	public function flushDatabase()
	{
		$this->getClient()->flushDB();
	}

	/**
	 * Sets a simple reverse index using a single hash that maps a field to the document id.
	 */
	public function setIndexSimpleHash($id, $group, $name, $data, $originalData, $metadata, Redis $pipeline = null) {
		if (null === $pipeline) {
			$pipeline = $this->getClient();
		}

		// If the value is new, or has been updated, write an index.
		if ($data[$name] && ($data[$name] != $originalData[$name])) {
			$field = $data[$name];
			$key = $group . ':by' . implode('', array_map('ucfirst', explode('_', $name)));
			$pipeline->hSet($key, $field, $id);
		}

		// If we had data already and the data has changed, delete the old index entry.
		if ($originalData[$name] && ($originalData[$name] != $data[$name])) {
			$field = $originalData[$name];
			$pipeline->hDel($key, $field);
		}
	}

	public function updateIndex($type, $id, $config, Redis $pipeline = null) {
		if (null === $pipeline) {
			$pipeline = $this->getClient();
		}

		$indexClass = 'Celsus_Db_Document_Redis_Index_' . ucfirst($type);
		$index = new $indexClass();

		$index->update($id, $config, $pipeline);
	}

	public function startPipeline() {
		return $this->getClient()->multi();
	}

	public function send(Redis $pipeline) {
		return $pipeline->exec();
	}

	/**
	 * Gets the client used to execute commands.
	 *
	 * @return Redis
	 */
	public function getClient() {
		if (null === $this->_client) {
			$this->_client = new Redis();
			$this->_client->connect($this->getHost(), $this->getPort(), $this->getTimeout());
			$this->_client->select($this->getDb());

			// If an exception was thrown in the middle of the last multi, a permanent connection doesn't
			// properly discard the transaction, so we do a transaction at the beginning to ensure a clean slate.
			// Fixed in phpredis 2.2.1, but that version has other more serious errors.
			// $this->_client->discard();
		}
		return $this->_client;
	}
}