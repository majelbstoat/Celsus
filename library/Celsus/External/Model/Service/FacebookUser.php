<?php

class Celsus_External_Model_Service_FacebookUser extends Celsus_Model_Service {

	protected static $_name = 'facebookUser';

	protected static $_underlying = null;

	protected static $_descriptiveField = 'name';

	protected static $_defaultFields = array (
		'first_name' => array(
			'type' => self::FIELD_TYPE_STRING,
			'title' => 'First Name',
			'description' => 'First Name',
		),
		'middle_name' => array(
			'type' => self::FIELD_TYPE_STRING,
			'title' => 'Middle Name',
			'description' => 'Middle Name',
		),
		'last_name' => array(
			'type' => self::FIELD_TYPE_STRING,
			'title' => 'Last Name',
			'description' => 'Last Name',
		),
		'email' => array(
			'type' => self::FIELD_TYPE_STRING,
			'title' => 'Email',
			'description' => 'Email',
		),
		'username' => array(
			'type' => self::FIELD_TYPE_STRING,
			'title' => 'Username',
			'description' => 'Username',
		),
		'gender' => array(
			'type' => self::FIELD_TYPE_STRING,
			'title' => 'Gender',
			'description' => "The user's gender",
		),
		'locale' => array(
			'type' => self::FIELD_TYPE_STRING,
			'title' => 'Locale',
			'description' => 'Locale',
		),
		'updated_time' => array(
			'type' => self::FIELD_TYPE_STRING,
			'title' => 'Updated Time',
			'description' => 'Updated Time',
		),
		'hometown' => array(
			'type' => self::FIELD_TYPE_STRING,
			'title' => 'Hometown',
			'description' => 'Hometown',
		),
	);

	public static function acquireAccessToken($authorisationCode, $callbackUrl) {
		return static::_underlying()->acquireAccessToken($authorisationCode, $callbackUrl);
	}

	public static function getProfileInformation($accessToken) {
		return static::_underlying()->multiple()->cache(array('basic', $accessToken))->getUserData($accessToken);
	}

}