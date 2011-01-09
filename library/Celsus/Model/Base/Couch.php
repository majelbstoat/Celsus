<?php

class Celsus_Model_Base_Couch implements Celsus_Model_Base_Interface {

	// This class will behave in a similar fashion to Celsus_Model_Base_DbTable
	// and reference a document, like the latter references a Zend_Db_Table_Row

	/**
	 * The adapter to use for this connection.
	 *
	 * @var Celsus_Db_Document_Adapter_Couch
	 */
	protected $_adapter;

	/**
	 * The adapter to use if it hasn't been set.
	 *
	 * @var unknown_type
	 */
	protected static $_defaultAdapter = null;

	/**
	 * The design document used for querying views.
	 *
	 * @var unknown_type
	 */
	protected $_designDocument = null;

	public function __construct(array $config = array()) {
		$this->_adapter = isset($config['adapter']) ? $config['adapter'] : self::getDefaultAdapter();
	}

	public static function setDefaultAdapter($adapter) {
		self::$_defaultAdapter = $adapter;
	}

	public static function getDefaultAdapter() {
		if (null === self::$_defaultAdapter) {
			self::setDefaultAdapter(Celsus_Db::getAdapter(Celsus_Db::getDefaultAdapterName()));
		}
		return self::$_defaultAdapter;
	}

	/**
	 * Finds records based on identifiers.
	 *
	 * @param array|string $identifiers.  The identifier of the record to find or a view definition.
	 * @return Celsus_Db_Document_CouchSet
	 */
	public function find() {
		$arguments = func_get_args();
		$identifiers = $arguments[0];

		if (!is_array($identifiers)) {
			$identifiers = array($identifiers);
		}

		return $this->getAdapter()->find($identifiers);
	}

	/**
	 * Returns a set of records from a view based on the supplied parameters.
	 *
	 * @throws Celsus_Exception
	 * @return Celsus_Db_Document_CouchSet
	 */
	public function fetchAll() {
		$arguments = func_get_args();
		$view = $arguments[0];
		if (!$view instanceof Celsus_Db_Document_View) {
			throw new Celsus_Exception("Must supply a valid view.");
		}
		return $this->getAdapter()->view($view);
	}

	/**
	 * Deletes from permanent storage, based on the supplied query.
	 * @param array|string $where
	 */
	public function delete($where) {
		throw new Celsus_Exception("Not $where implemented");
	}

	/**
	 * Creates a new record, filled with default data, ready to be populated.
	 * @return Celsus_Data_Object
	 */
	public function createRecord(array $data = array()) {
		$record = new Celsus_Db_Document_Couch(array(
			'adapter' => $this->getAdapter(),
			'data' => $data
		));
		return $record;
	}

	/**
	 * Gets the adapter for this base.
	 *
	 * @return Celsus_Db_Document_Adapter_Couch
	 */
	public function getAdapter() {
		return $this->_adapter;
	}
}

?>