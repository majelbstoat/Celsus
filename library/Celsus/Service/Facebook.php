<?php

class Celsus_Service_Facebook {

	const DATA_BASIC = '';

	protected static $_facebookAdapter = null;

	public static function getFacebookAdapter() {
		if (null === self::$_facebookAdapter) {
			self::$_facebookAdapter = Celsus_Db::getAdapter('facebook');
		}
		return self::$_facebookAdapter;
	}

	public static function setFacebookAdapter($facebookAdapter) {
		self::$_facebookAdapter = $facebookAdapter;
	}

	public static function acquireAccessToken($authorisationCode, $callbackPath) {
		return self::getFacebookAdapter()->acquireAccessToken($authorisationCode, $callbackPath);
	}

	public static function find($accessTokens) {
		return self::getFacebookAdapter()->getUserData($accessTokens, self::DATA_BASIC);
	}

	public static function getUserData($accessTokens, $dataType) {
		return self::getFacebookAdapter()->getUserData($accessTokens, $dataType);
	}

}