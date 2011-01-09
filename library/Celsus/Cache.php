<?php

class Celsus_Cache {

	protected static $_backend = 'File';

	protected static $_backendOptions = array(
		'cache_dir' => CACHE_PATH
	);

	/**
	 * The cache object.
	 *
	 * @var Zend_Cache_Frontend
	 */
	protected static $_cache = null;

	/**
	 * Disabled by default.
	 *
	 * @var boolean
	 */
	protected static $_enabled = false;

	protected static $_frontend = 'Core';

	protected static $_frontendOptions = array(
		'logging' => true,
		'automatic_serialization' => true
	);

	/**
	 * Hashes a key by multi-tenant name to prevent cross-tenant cache retrieval.  Does not hash if this is not a
	 * multi-tenanted application, or if the key begins with --shared--.
	 *
	 * @param string $key
	 * @return string
	 */
	protected static function _multiTenantKey($key) {
		$tenant = Celsus_Application::getTenantName();
		if ($tenant && ('--shared--' != substr($key, 0, 10))) {
			$key = md5($tenant . $key);
		}
		return $key;
	}

	public static function isEnabled() {
		return self::$_enabled;
	}

	public static function load($key) {
		if (!self::isEnabled()) {
			return false;
		}
		return self::$_cache->load(self::_multiTenantKey($key));
	}

	public static function save($data, $key) {
		if (self::isEnabled()) {
			self::$_cache->save($data, self::_multiTenantKey($key));
		}
	}

	public static function delete($key) {
		self::$_cache->delete(self::_multiTenantKey($key));
	}

	public static function initialise(array $options = null) {
		self::$_cache = null;
		if (!$options) {
			$config = Zend_Registry::get('config')->cache;
			if ($config) {
				$options = $config->toArray();
			}
		}

		if (array_key_exists('frontend', $options)) {
			self::$_frontend = $options['frontend']['type'];

			if (array_key_exists('options', $options['frontend'])) {
				self::$_frontendOptions = $options['frontend']['options'];
			}
		}

		if (array_key_exists('backend', $options)) {
			self::$_backend = $options['backend']['type'];

			if (array_key_exists('options', $options['backend'])) {
				self::$_backendOptions = $options['backend']['options'];
			}
		}

		// Get the cache object.
		require_once 'Zend/Cache.php';

		// Workaround for Zend Bug ZF-10189
		require_once 'Zend/Log.php';

		self::$_cache = Zend_Cache::factory(self::$_frontend, self::$_backend, self::$_frontendOptions, self::$_backendOptions);
		self::$_enabled = true;
	}

}