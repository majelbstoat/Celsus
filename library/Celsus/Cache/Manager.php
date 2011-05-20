<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Cache
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Manager.php 69 2010-09-08 12:32:03Z jamie $
 */
require_once 'Zend/Cache/Manager.php';
/**
 * Multi-tenanted cache support
 *
 * @category Celsus
 * @package Celsus_Cache
 */
class Celsus_Cache_Manager extends Zend_Cache_Manager {

	protected static $_instance = null;

	protected static $_shared = false;

	protected static $_targetCache = 'default';

	/**
	 * Provides a clean facade to the caches, while allowing us to multitenant keys.
	 *
	 * Call it like:
	 *
	 * Celsus_Cache_Manager::cache('routes')->save($key, $data);
	 *
	 * @param string $name
	 * @return Celsus_Cache_Manager
	 */
	public static function cache($name = 'default') {
		$manager = self::getInstance();
		self::setTargetCache($name);
		return $manager;
	}

	/**
	 * Returns an instance of the object.
	 *
	 * @return Celsus_Cache_Manager
	 */
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function initialise() {
		$manager = self::getInstance();
		$cacheConfigs = Zend_Registry::get('config')->cache;
		foreach ($cacheConfigs as $name => $config) {
			if ($manager->hasCache($name)) {
				$manager->setTemplateOptions($name, $config);
			} else {
				$manager->setCacheTemplate($name, $config);
			}
		}
	}

	public static function addCacheTemplate($name, $options) {
		$manager = self::getInstance();
		return $manager->setCacheTemplate($name, $options);
	}

	public static function setTargetCache($name) {
		self::$_targetCache = $name;
	}

	public function isEnabled($name) {
		return (isset($this->_optionTemplates[$name]) && isset($this->_optionTemplates[$name]['enabled']) && (true == $this->_optionTemplates[$name]['enabled']));
	}

	/**
	 * Hashes a key by multi-tenant name to prevent cross-tenant cache retrieval.  Does not hash if this is not a
	 * multi-tenanted application, or if the manager is set to perform the next action shared.
	 *
	 * @param string $key
	 * @return string
	 */
	public static function multiTenantKey($key) {
		$tenant = Celsus_Application::getTenantName();
		if (!self::isShared()) {
			$key = $tenant . $key;
		}
		return $key;
	}

	public static function isShared() {
		return self::$_shared;
		return  (!self::$_shared && Celsus_Application::getTenantName()) ? false : true;
	}

	public static function getTargetCache() {
		return self::$_targetCache;
	}

	public function shared($shared = true) {
		self::$_shared = $shared;
		return $this;
	}

	public function load($key) {
		if (!$this->isEnabled(self::$_targetCache)) {
			return false;
		}
		$result = $this->getCache(self::$_targetCache)->load(self::multiTenantKey($key));
		self::$_shared = false;
		return $result;
	}

	public function save($data, $key, $tags = array()) {
		if ($this->isEnabled(self::$_targetCache)) {
			$this->getCache(self::$_targetCache)->save($data, self::multiTenantKey($key), $tags);
		}
		self::$_shared = false;
	}

	public function delete($key) {
		if ($this->isEnabled(self::$_targetCache)) {
			$this->getCache(self::$_targetCache)->delete(self::multiTenantKey($key));
		}
		self::$_shared = false;
	}

	/**
	 * Also merges enabled flag.
	 *
	 * @param  array $current
	 * @param  array $options
	 * @return array
	 */
	protected function _mergeOptions(array $current, array $options) {

		// Do the bulk of the merging.
		$current = parent::_mergeOptions($current, $options);

		// Test for an enabled flag.
		if (isset($options['enabled'])) {
			$current['enabled'] = $options['enabled'];
		}

		if (isset($options['frontend']['customFrontendNaming'])) {
			$current['frontend']['customFrontendNaming'] = $options['frontend']['customFrontendNaming'];
		}

		if (isset($options['backend']['customBackendNaming'])) {
			$current['backend']['customBackendNaming'] = $options['backend']['customBackendNaming'];
		}

		return $current;
	}
}