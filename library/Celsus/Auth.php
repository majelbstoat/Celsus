<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Auth.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Authentication functionality
 *
 * @defgroup Celsus_Auth Celsus Authentication
 */

/**
 * Defines authentication, and allows adapters to be switched for mocking.
 *
 * @ingroup Celsus_Auth
 */
class Celsus_Auth extends Zend_Auth {

	protected static $_authAdapter = null;

	public static function setAuthAdapter(Zend_Auth_Adapter_Interface $authAdapter) {
		self::$_authAdapter = $authAdapter;
	}

	public static function getAuthAdapter() {
		if (null == self::$_authAdapter) {
			throw new Celsus_Exception("Auth adapter has not been set!");
		}
		return self::$_authAdapter;
	}

	public static function resetAuthAdapter() {
		self::$_authAdapter = null;
	}
}
?>