<?php

class Celsus_Model_Base_Facebook extends Celsus_Model_Base {

	const BACKEND_TYPE = 'facebook';

	protected static $_dataClass = 'Celsus_Db_Document_Facebook';

	/**
	 * The fields that are present in the underlying representation of this model.
	 *
	 * @var array
	 */
	protected $_fields = null;

	protected static function _getDefaultAdapter() {
		return Celsus_Db::getAdapter('facebook');
	}

	public function acquireAccessToken($authorisationCode, $callbackUrl) {
		return self::_getAdapter()->acquireAccessToken($authorisationCode, $callbackUrl);
	}

	public function getUserData($accessToken, $dataType = Celsus_Db_Document_Adapter_Facebook::DATA_TYPE_PROFILE_INFO) {
		return self::_getAdapter()->getUserData($accessToken, $dataType);
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
}