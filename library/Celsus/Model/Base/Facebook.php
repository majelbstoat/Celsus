<?php

class Celsus_Model_Base_Facebook extends Celsus_Model_Base {

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

	protected static $_dataClass = 'Celsus_Db_Document_Facebook';

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

	/**
	 * Returns the fields that represent this model on Facebook.
	 *
	 * @return array
	 */
	public function getFields() {
		return $this->_fields;
	}

	protected function _getDefaults() {
		$fields = $this->getFields();
		return array_combine($fields, array_fill(0, count($fields), null));
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