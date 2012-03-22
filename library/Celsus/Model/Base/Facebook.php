<?php

class Celsus_Model_Base_Facebook implements Celsus_Model_Base_Interface {

	// This class will behave in a similar fashion to Celsus_Model_Base_DbTable
	// and reference a document, like the latter references a Zend_Db_Table_Row

	/**
	 * The adapter to use for this connection.
	 *
	 * @var Celsus_Db_Document_Adapter_Facebook
	 */
	protected $_adapter;

	/**
	 * The adapter to use if it hasn't been set.
	 *
	 * @var unknown_type
	 */
	protected static $_defaultAdapter = null;

	/**
	 * The fields that are present in the underlying representation of this model.
	 *
	 * @var array
	 */
	protected $_fields = null;

	public function __construct(array $config = array()) {
		$this->_adapter = isset($config['adapter']) ? $config['adapter'] : self::getDefaultAdapter();
	}

	public static function setDefaultAdapter($adapter) {
		self::$_defaultAdapter = $adapter;
	}

	public static function getDefaultAdapter() {
		if (null === self::$_defaultAdapter) {
			self::setDefaultAdapter(Celsus_Db::getAdapter('facebook'));
		}
		return self::$_defaultAdapter;
	}

	/**
	 * Finds records based on access tokens.
	 *
	 * @param array|string $accessTokens.  The identifier of the record to find.
	 * @return Celsus_Db_Document_CouchSet
	 */
	public function find() {
		$arguments = func_get_args();
		$accessTokens = $arguments[0];

		if (!is_array($accessTokens)) {
			$identifiers = array($accessTokens);
		}

		return $this->getAdapter()->find($accessTokens);
	}

	/**
	 * Returns a set of records from a view based on the supplied parameters.
	 *
	 * @throws Celsus_Exception
	 * @return Celsus_Db_Document_FacebookSet
	 */
	public function fetchAll() {
		$arguments = func_get_args();
		$view = $arguments[0];
		throw new Celsus_Exception("Not implemented");
	}

	public function acquireAccessToken($authorisationCode, $callbackPath) {
		return $this->getAdapter()->acquireAccessToken($authorisationCode, $callbackPath);
	}

	public function getUserData($accessToken, $dataType) {
		return $this->getAdapter()->getUserData($accessToken, $dataType);
	}

	/**
	 * Deletes from permanent storage, based on the supplied query.
	 * @param array|string $where
	 */
	public function delete($where) {
		throw new Celsus_Exception("Not implemented");
	}

	public function createRecord(array $data = array()) {
		$fields = $this->getFields();
		$defaults = array_combine($fields, array_fill(0, count($fields), null));

		$record = new Celsus_Db_Document_Facebook(array(
			'adapter' => $this->getAdapter(),
			'data' => array_merge($defaults, $data)
		));
		return $record;
	}

	/**
	 * Returns the fields that represent this model on Facebook.
	 *
	 * @return array
	 */
	public function getFields() {
		return $this->_fields;
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