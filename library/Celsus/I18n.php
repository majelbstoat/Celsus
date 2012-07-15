<?php

class Celsus_I18n {

	const CONFIG_MESSAGES = 'i18n/%s/';

	const LOCALE_EN_GB = 'en_GB';

	protected static $_messages = array();

	protected static $_locale = 'en_GB';

	protected static $_localePath = null;

	protected static $_validLocales = array(
		'en_GB'
	);

	public static function getLocale() {
		return self::$_locale;
	}

	public static function setLocale($locale) {
		self::$_locale = $locale;
		self::$_localePath = CONFIG_PATH . '/' . sprintf(self::CONFIG_MESSAGES, $locale);
	}

	public static function getMessages($type) {
		if (!isset(self::$_messages[$type])) {
			self::$_messages[$type] = new Zend_Config_Yaml(self::$_localePath . "/$type.yaml");
		}
		return self::$_messages[$type];
	}
}