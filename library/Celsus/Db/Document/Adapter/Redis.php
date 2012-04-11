<?php

class Celsus_Db_Document_Adapter_Redis {

	// Redis defines 16 numerically named databases.  By design,
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

	public function find($identifiers) {
		if (!is_array($identifiers)) {
			$identifiers = array($identifiers);
		}

		$client = $this->getClient()->multi();
		foreach ($identifiers as $identifier) {
			$client->hGetAll($identifier);
		}

		$data = $client->exec();
		if ($data) {
			return new Celsus_Db_Document_Set_Redis(array(
				'adapter' => $this,
				'data' => $data
			));
		}

		return null;
	}

	/**
	 * Finds an identifier using a secondary index.
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function query($key, $value) {
		$identifier = $this->getClient()->hGet($key, $value);

		return $identifier ? $this->find($identifier) : null;
	}

	/**
	 * Acquires a globally unique ID from a well known atomic object counter.
	 *
	 * @return int
	 */
	public function acquireId() {
		return $this->getClient()->incr(self::KEY_OBJECT_COUNTER);
	}

	public function save(Celsus_Db_Document_Redis $document)
	{
		$id = $document->getId();
		$data = $document->toArray();
		$type = $data['_type'];
		$data['_created'] = microtime(true);

		$result = $this->getClient()
			->multi()
			->zAdd($type, $data['_created'], $id)
			->hMset($id, $data)
			->exec();

		if (!$result) {
			throw new Celsus_Exception("There was an error saving the object with ID $id");
		}
	}

	public function flushDatabase()
	{
		$this->getClient()->flushDB();
	}

	/**
	 * Sets a simple reverse index using a single hash that maps a field to the document id.
	 *
	 * @param string $key
	 * @param string $field
	 * @param string $id
	 */
	public function setIndexSimpleHash($id, $group, $name, $data, $originalData, Redis $pipeline = null) {
		if (null === $pipeline) {
			$pipeline = $this->getClient();
		}

		$field = $data[$name];
		$key = $group . ':by' . implode('', array_map('ucfirst', explode('_', $name)));

		$pipeline->hMset($key, array($field => $id));
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
			$this->_client->pconnect($this->getHost(), $this->getPort(), $this->getTimeout());
			$this->_client->select($this->getDb());
		}
		return $this->_client;
	}

}