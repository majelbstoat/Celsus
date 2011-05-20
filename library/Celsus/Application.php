<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Application.php 69 2010-09-08 12:32:03Z jamie $
 */

require_once 'Zend/Application.php';

/**
 * Application functionality
 *
 * @defgroup Celsus_Application Celsus Application
 */

/**
 * Defines an application that can lazily-load a config from a cache
 * or definition file.
 *
 * @ingroup Celsus_Application
 */
abstract class Celsus_Application extends Zend_Application {

	/**
	 * Defines the cache where routes and configs are stored.  By default
	 * lives in its own cache.
	 *
	 * @var string
	 */
	protected $_bootstrapCacheName = 'bootstrap';

	/**
	 * The options used to populate the cache.
	 *
	 * @var array
	 */
	protected $_bootstrapCacheTemplate = null;

	protected static $_rootUrl = null;

	protected static $_tenantName = null;

	protected static $_scheme = null;

	protected static $_host = null;

	/**
	 * Whether or not to load config and routes from cache.
	 *
	 * @var boolean
	 */
	protected $_useCacheForBootstrap = false;

	public static function getTenantName() {
		return self::$_tenantName;
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
	 * @param $useCacheForBootstrap
	 */
	public function __construct($environment, array $configPaths, $useCacheForBootstrap = true) {
		// Hydrate or create an application instance.

		require_once 'Zend/Config/Ini.php';

		$config = null;
		$serialisedPaths = serialize($configPaths);
		$configCacheKey = "__$environment" . "__config__";

		require_once 'Celsus/Cache/Manager.php';

		// First see if we can find a config in the cache.
		$this->_bootstrapCacheTemplate['enabled'] = $useCacheForBootstrap;
		Celsus_Cache_Manager::addCacheTemplate($this->_bootstrapCacheName, $this->_bootstrapCacheTemplate);

		$config = Celsus_Cache_Manager::cache($this->_bootstrapCacheName)->shared()->load($configCacheKey);
		if (!$config) {
			$configPath = array_shift($configPaths);

			$config = new Zend_Config_Ini($configPath, $environment, true);
			foreach ($configPaths as $configPath) {
				$config->merge(new Zend_Config_Ini($configPath, $environment));
			}

			Celsus_Cache_Manager::cache($this->_bootstrapCacheName)->shared()->save($config, $configCacheKey, array('config'));
		}

		require_once 'Zend/Registry.php';
		Zend_Registry::set('config', $config);

		self::$_scheme = $config->url->scheme;
		self::$_host = $config->url->host;

		// Store a reference to the instance, should we need to retrieve it later.
		return parent::__construct($environment, $config);
	}

	public static function rootUrl() {
		return self::$_scheme . self::$_host;
	}

	public static function tenantUrl() {
		return self::$_scheme . self::$_tenantName . '.' . self::$_host;
	}

	/**
	 * Gets routes for the application.
	 *
	 * @return Zend_Config_Ini
	 */
	public function getRoutes() {

		// Hydrate or generate application routes.
		$routesCacheKey = "__routes__";
		$routes = Celsus_Cache_Manager::cache($this->_bootstrapCacheName)->shared()->load($routesCacheKey);
		if (!$routes) {
			$config = Zend_Registry::get('config');
			$routes = new Zend_Config_Ini($config->routes->path);
			Celsus_Cache_Manager::cache($this->_bootstrapCacheName)->shared()->save($routes, $routesCacheKey, array('routes'));
		}
		return $routes;
	}

	/**
	 * Bootstraps the application, and allows for excluding of resources.
	 *
	 * @param null|string|array $resource
	 * @param array $excludedResources
	 */
	public function bootstrap($resource = null, array $excludedResources = array()) {
		return $this->getBootstrap()->setExcludedResources($excludedResources)->bootstrap($resource);
	}
}
