<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Application
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

require_once 'Zend/Application.php';

/**
 * Provides an application definition that can optionally be cached.
 *
 * @category Celsus
 * @package Celsus_Application
 */
abstract class Celsus_Application extends Zend_Application {

	/**
	 * The options used to populate the cache.
	 *
	 * @var array
	 */
	protected $_cacheOptions = null;

	/**
	 * Whether or not to load config and routes from cache.
	 *
	 * @var boolean
	 */
	protected $_useCache = false;

	protected static $_rootUrl = null;

	protected static $_tenantName = null;

	public static function getTenantName() {
		return self::_tenantName;
	}

	public static function setTenantName($tenantName) {
		self::$_tenantName = $tenantName;
	}

	public static function isDevelopment() {
		return ('development' == APPLICATION_ENV);
	}

	public static function isProduction() {
		return ('production' == APPLICATION_ENV);
	}

	public static function isTesting() {
		return ('testing' == APPLICATION_ENV);
	}

	/**
	 * Create a new application instance.  Tries to load from cache where possible.
	 *
	 * @param $environment
	 * @param $configPaths
	 */
	public function __construct($environment, array $configPaths, $useCache = false) {
		// Hydrate or create an application instance.
		$this->_useCache = $useCache;

		require_once 'Zend/Config/Ini.php';

		require_once 'Celsus/Cache.php';

		$config = null;
		$serialisedPaths = serialize($configPaths);
		$configCacheKey = md5(APPLICATION_NAME . "-$serialisedPaths-$environment-config");

		if ($this->_useCache) {
			// First see if we can find a config in the cache.
			Celsus_Cache::initialise($this->_cacheOptions);

			$config = Celsus_Cache::load($configCacheKey);
		}

		if (!$config) {
			$configPath = array_shift($configPaths);

			$config = new Zend_Config_Ini($configPath, $environment, true);
			foreach ($configPaths as $configPath) {
				$config->merge(new Zend_Config_Ini($configPath, $environment));
			}
			if ($this->_useCache) {
				Celsus_Cache::save($config, $configCacheKey);
			}
		}

		require_once 'Zend/Registry.php';
		Zend_Registry::set('config', $config);

		self::$_rootUrl = $config->url->scheme . $config->url->host;

		// Store a reference to the instance, should we need to retrieve it later.
		return parent::__construct($environment, $config);
	}

	public static function rootUrl() {
		return self::$_rootUrl;
	}

	/**
	 * Gets routes for the application.
	 *
	 * @return Zend_Config_Ini
	 */
	public function getRoutes() {

		// Hydrate or generate application routes.
		if ($this->_useCache) {
			$routesCacheKey = md5(APPLICATION_NAME . "-routes");
			$routes = Celsus_Cache::load($routesCacheKey);
			if (!$routes) {
				$config = Zend_Registry::get('config');
				$routes = new Zend_Config_Ini($config->routes->path);
				Celsus_Cache::save($routes, $routesCacheKey);
			}
		} else {
			$config = Zend_Registry::get('config');
			$routes = new Zend_Config_Ini($config->routes->path);
		}
		return $routes;
	}

}
