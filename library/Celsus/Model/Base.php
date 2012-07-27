<?php

abstract class Celsus_Model_Base implements Celsus_Model_Base_Interface {

	protected $_secondaryIndices = array();

	public static function setDefaultAdapter($adapter) {
		static::$_defaultAdapter = $adapter;
	}

	public static function getDefaultAdapter() {
		if (null === static::$_defaultAdapter) {
			self::setDefaultAdapter(Celsus_Db::getAdapter(Celsus_Db::getDefaultAdapterName()));
		}
		return static::$_defaultAdapter;
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

		$fields = $this->getFields();
		return $this->getAdapter()->find($identifiers);
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
			'adapter' => $this->getAdapter(),
			'data' => array_merge($defaults, $data)
		));

		return $record;
	}

	public function getIndices() {
		return $this->_secondaryIndices;
	}

	public function updateIndices($id, $data, $originalData, $metadata) {}

}
