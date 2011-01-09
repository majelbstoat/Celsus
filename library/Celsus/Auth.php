<?php

class Celsus_Auth extends Zend_Auth {

	protected static $_authAdapter = null;

	public static function setAuthAdapter($authAdapter) {
		self::$_authAdapter = $authAdapter;
	}

	public static function getAuthAdapter() {
		if (null == self::$_authAdapter) {
			//self::$_authAdapter = new Celsus_Auth_Adapter_UserService('ForceField_Model_Service_User');
			self::$_authAdapter = new Celsus_Auth_Adapter_DbTable(Celsus_Db::getAdapter(Celsus_Db::getDefaultAdapterName()), 't_user AS u', 'p.email', 'u.password', 'MD5(?)');
			self::$_authAdapter->setJoin('t_person AS p', 'u.person_id = p.id');
		}
		return self::$_authAdapter;
	}

	public static function resetAuthAdapter() {
		self::$_authAdapter = null;
	}

}
?>