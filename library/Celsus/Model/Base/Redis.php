<?php

class Celsus_Model_Base_Redis extends Celsus_Model_Base {

	const INDEX_TYPE_SIMPLE_HASH = 'simpleHash';
	const INDEX_TYPE_SET_MEMBERS = 'setMembers';

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
		throw new Celsus_Exception("Not implemented: $where");
	}

	protected function _getDefaults() {
		$fields = $this->getFields();
		$defaults = array_combine($fields, array_fill(0, count($fields), null));
		$defaults['_type'] = $this->_name;

		return $defaults;
	}

	/**
	 * Returns the fields that represent this model in the underlying redis database.
	 *
	 * @return array
	 */
	public function getFields() {
		return array_merge(array('id'), static::$_fields, array('_type'));
	}

	public function fetchAll() {
		$query = func_get_arg(0);
		return $this->getAdapter()->query($query);
	}

	/**
	 * Gets the adapter for this base.
	 *
	 * @return Celsus_Db_Document_Adapter_Redis
	 */
	public function getAdapter() {
		return $this->_adapter;
	}

	public function updateIndices($id, $data, $originalData) {
		$indices = $this->getIndices();
		if ($indices) {
			$adapter = $this->getAdapter();
			$pipeline = $adapter->startPipeline();
			foreach ($indices as $field => $types) {
				if (!is_array($types)) {
					$types = array($types);
				}
				foreach ($types as $type) {
					$method = 'setIndex' . ucfirst($type);
					call_user_func_array(array($adapter, $method), array($id, $this->_name, $field, $data, $originalData, $pipeline));
				}
			}
			$adapter->send($pipeline);
		}
	}

}