<?php

class Celsus_Test_Db_Redis implements Celsus_Test_Db_Interface {

	const TYPE_SET = 'set';
	const TYPE_ZSET = 'zset';
	const TYPE_HASH = 'hash';
	const TYPE_LIST = 'list';
	const TYPE_STRING = 'string';

	const EXPIRE_NEVER = -1;

	protected $_adapter = null;

	protected $_path = null;

	public function __construct(Celsus_Db_Document_Adapter_Redis $adapter)
	{
		$this->_adapter = $adapter;
	}

	public function reset() {
		$client = $this->_adapter->getClient();

		$client->echo("--- Resetting Data ---");
		$client->flushDB();
		$client->echo("--- Done ---");
	}

	public function dump($name) {

		$client = $this->_adapter->getClient();

		$keys = $client->keys('*');

		$pipeline = $this->_adapter->startPipeline();

		foreach ($keys as $key) {
			$pipeline->type($key);
		}

		$types = $this->_adapter->send($pipeline);

		$keyTypes = array_combine($keys, $types);

		$data = array();

		foreach ($keyTypes as $key => $type) {

			$item = array();
			switch ($type) {

				case Redis::REDIS_SET:
					$item['type'] = self::TYPE_SET;
					$item['value'] = $client->sMembers($key);
					break;

				case Redis::REDIS_ZSET:
					$item['type'] = self::TYPE_ZSET;
					$item['value'] = $client->zRange($key, 0, -1);
					break;

				case Redis::REDIS_HASH:
					$item['type'] = self::TYPE_HASH;
					$item['value'] = $client->hGetAll($key);
					break;

				case Redis::REDIS_LIST:
					$item['type'] = self::TYPE_LIST;
					$item['value'] = $client->lRange($key, 0, -1);
					break;

				case Redis::REDIS_STRING:
					$item['type'] = self::TYPE_STRING;
					$item['value'] = $client->get($key);
					break;
			}

			$item['ttl'] = (string) $client->ttl($key);

			$data[$key] = $item;
		}

 		$json = Zend_Json::encode($data);
 		$filename = $this->getFilename($name);

		file_put_contents($filename, $json);
	}

	public function load($name) {

		$filename = $this->getFilename($name);
		$json = file_get_contents($filename);

		$data = Zend_Json::decode($json);

		$client = $this->_adapter->getClient();
		$client->echo("--- Seeding Database with '$name' ---");

		$pipeline = $this->_adapter->startPipeline();

		$pipeline->flushDB();

		foreach ($data as $key => $definition) {

			switch ($definition['type']) {

				case self::TYPE_SET:
					foreach ($definition['value'] as $member) {
						$pipeline->sAdd($key, $member);
					}
					break;

				case self::TYPE_ZSET:
					foreach ($definition['value'] as $score => $member) {
						$pipeline->zAdd($key, $score, $member);
					}
					break;

				case self::TYPE_HASH:
					$pipeline->hMset($key, $definition['value']);
					break;

				case self::TYPE_LIST:
					foreach ($definition['value'] as $member) {
						$pipeline->rPush($key, $member);
					}
					break;

				case self::TYPE_STRING:
					$pipeline->set($key, $definition['value']);
					break;

				default:
					throw new Celsus_Exception("Invalid data type: " . $definition['type'], Celsus_Http::INTERNAL_SERVER_ERROR);
					break;
			}

			$ttl = (int) $definition['ttl'];
			if (self::EXPIRE_NEVER !== $ttl) {
				$pipeline->expire($key, $ttl);
			}
		}

		$this->_adapter->send($pipeline);

		$client->echo("--- Finished Seeding with '$name' ---");
	}

	protected function getPath() {
		if (null === $this->_path) {
			$this->_path = FIXTURES_PATH . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'redis';
		}

		return $this->_path;
	}

	protected function getFilename($name) {
		return $this->getPath() . DIRECTORY_SEPARATOR . strtolower($name) . '.json';
	}
}