<?php

class Celsus_External_Model_Service_FacebookUser extends Celsus_Model_Service {

	const DATA_TYPE_PERMISSIONS = 'permissions';
	const DATA_TYPE_PROFILE_INFO = '';
	const DATA_TYPE_FRIENDS = 'friends';

	protected static $_name = 'facebookUser';

	protected static $_underlying = null;

	protected static $_descriptiveField = array('name');

	// @todo These are wrong.
	protected static $_defaultFields = array (
		'name' => array(),
		'summary' => array(),
		'title' => array(),
		'description' => array(),
		'group' => array(),
	);

	public static function acquireAccessToken($authorisationCode, $callbackUrl) {
		return static::_underlying()->acquireAccessToken($authorisationCode, $callbackUrl);
	}

	public static function getPermissions($accessToken) {
		return static::_underlying()->cache(array(self::DATA_TYPE_PERMISSIONS, $accessToken))->getUserData($accessToken, self::DATA_TYPE_PERMISSIONS);
	}

	public static function getProfileInformation($accessToken) {
		return static::_underlying()->cache(array('basic', $accessToken))->getUserData($accessToken, self::DATA_TYPE_PROFILE_INFO);
	}

}