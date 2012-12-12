<?php

abstract class Celsus_Model_Base implements Celsus_Model_Base_Interface {

	/**
	 * The database adapters available to the application.
	 *
	 * @var array
	 */
	protected static $_adapters = array();

	protected $_secondaryIndices = array();

	protected static function _getDefaultAdapter() {
		return Celsus_Db::getAdapter(Celsus_Db::getDefaultAdapterName());
	}

	/**
	 * Finds records based on identifiers.
	 *
	 * @param array|string $identifiers.  The identifier of the record to find or a query or view to get one.
	 * @return Celsus_Db_Document_Set
	 */
	public function find() {
		$arguments = func_get_args();
		$identifiers = $arguments[0];

		if (!is_array($identifiers)) {
			$identifiers = array($identifiers);
		}

		return self::_getAdapter()->find($identifiers);
	}

	/**
	 * Returns a set of records from a view based on the supplied parameters.
	 *
	 * @throws Celsus_Exception
	 * @return Celsus_Db_Document_Set
	 */
	public function fetchAll() {
		throw new Celsus_Exception("Not implemented");
	}

	/**
	 * Creates a new record, filled with default data, ready to be populated.
	 *
	 * @return Celsus_Db_Document_Redis
	 */
	public function createRecord(array $data = array()) {
		$defaults = $this->_getDefaults();
		$dataClass = static::$_dataClass;

		$record = new $dataClass(array(
			'adapter' => self::_getAdapter(),
			'data' => array_merge($defaults, $data)
		));

		return $record;
	}

	public function getIndices() {
		return $this->_secondaryIndices;
	}

	/**
	 * Gets the adapter for this base.
	 *
	 * @return Celsus_Db_Document_Adapter_Redis
	 */
	protected static function _getAdapter() {
		if (!isset(self::$_adapters[static::BACKEND_TYPE])) {
			self::$_adapters[static::BACKEND_TYPE] = static::_getDefaultAdapter();
		}
		return self::$_adapters[static::BACKEND_TYPE];
	}

	public function updateIndices($id, $data, $originalData, $metadata, Redis $pipeline = null) {}
}
