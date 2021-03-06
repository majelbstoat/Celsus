<?php

class Celsus_Model_Base_Couch implements Celsus_Model_Base_Interface {

	const BACKEND_TYPE = 'couch';

	/**
	 * The adapter to use if it hasn't been set.
	 *
	 * @var unknown_type
	 */
	protected static $_defaultAdapter = null;

	/**
	 * The design document used for querying views.
	 *
	 * @var string
	 */
	protected $_designDocument = null;

	public function __construct(array $config = array()) {
		$this->_adapter = isset($config['adapter']) ? $config['adapter'] : self::getDefaultAdapter();
	}

	public static function setDefaultAdapter($adapter) {
		self::$_defaultAdapter = $adapter;
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

		$fields = $this->getFields();
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
		throw new Celsus_Exception("Not implemented: $where");
	}

	/**
	 * Creates a new record, filled with default data, ready to be populated.
	 *
	 * Note that this is a little restrictive when compared to the free-for-all nature
	 * often assumed with NoSQL databases.  However, we still need some idea of the structure
	 * of the data to instantiate the Celsus_Data_Object later and we want to retain the
	 * safeguards on just adding arbitrary data.
	 *
	 * (Future application requirements may force us to rethink this and allow Celsus_Data_Objects
	 * with no pre-defined schema, however we could easily enforce a CDO with a single field 'data'
	 * which could contain arbitrary values.)
	 *
	 * @return Celsus_Db_Document_Couch
	 */
	public function createRecord(array $data = array()) {
		$fields = $this->getFields();
		$defaults = array_combine($fields, array_fill(0, count($fields), null));
		$defaults['type'] = $this->_name;

		$record = new Celsus_Db_Document_Couch(array(
			'adapter' => $this->getAdapter(),
			'data' => array_merge($defaults, $data)
		));
		return $record;
	}

	/**
	 * Returns the fields that represent this model in the underlying couchdb database.
	 *
	 * Given that CouchDB documents are schema-less, relying on a schema document in the database
	 * is very brittle and deletion of that document will break the entire model, therefore
	 * we enumerate the fields directly in code.
	 *
	 * @return array
	 */
	public function getFields() {
		return array_merge(array('_id', '_rev'), static::$_fields, array('type'));
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