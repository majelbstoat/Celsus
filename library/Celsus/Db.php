<?php

class Celsus_Db {

	/**
	 * The default adapter for the application.
	 *
	 * @var string
	 */
	protected static $_defaultAdapterName = null;

	/**
	 * Whether or not to profile database queries.
	 *
	 * @var boolean
	 */
	protected static $_profiling = false;

	/**
	 * The profiler to log database queries to.
	 *
	 * @var Zend_Db_Profiler_Firebug
	 */
	protected static $_profiler = null;

	public static function setDefaultAdapterName($adapter) {
		if (!is_string($adapter)) {
			throw new Celsus_Exception("$adapter is not a valid string to use as an adapter name.");
		}
		self::$_defaultAdapterName = $adapter;
	}

	public static function getDefaultAdapterName() {
		if (null === self::$_defaultAdapterName) {
			throw new Celsus_Exception("Default adapter name has not been set.");
		}
		return self::$_defaultAdapterName;
	}

	/**
	 * Instantiates a database adapter handle.
	 *
	 * @param string $name The internal name of the database to connect to.
	 * @return mixed
	 */
	public static function getAdapter($name) {
		if (Zend_Registry::isRegistered('databases')) {
			$databaseRegistry = Zend_Registry::get('databases');
		}

		if (!isset($databaseRegistry) || !isset($databaseRegistry->$name)) {
			$instance = Zend_Registry::getInstance();
			$config = Zend_Registry::get('config')->database->$name;
			if (null == $config) {
				throw new Celsus_Exception("$name is not a valid database to connect to.");
			}
			$connection = $config->connection->toArray();

			$type = $config->type ? $config->type : 'relational';
			$factory = 'Celsus_Db_' . ucfirst($type);
			if (!class_exists($factory, true)) {
				throw new Celsus_Exception($type . " is not a valid database type");
			}

			$db = call_user_func_array(array($factory, 'factory'), array($config->engine, $connection));

			$databaseRegistry = new StdClass;
			$databaseRegistry->$name = $db;
			Zend_Registry::set('databases', $databaseRegistry);
		}
		return $databaseRegistry->$name;
	}

	/**
	 * Returns an array of the database adapters that have been loaded so far.
	 *
	 * @return array
	 */
	public static function getLoadedAdapters() {
		if (!Zend_Registry::isRegistered('databases')) {
			return array();
		}
		return array_keys((array) Zend_Registry::get('databases'));
	}

	/**
	 * Enables profiling for all existing and future database connections.
	 */
	public static function enableProfiling() {
		foreach (self::getLoadedAdapters() as $adapter) {
			self::getAdapter($adapter)->setProfiler(self::getProfiler());
		}
		self::$_profiling = true;
	}

	/**
	 * Returns the Firebug profiler.
	 *
	 * @return unknown
	 */
	public static function getProfiler() {
		if (null == self::$_profiler) {
			self::$_profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
			self::$_profiler->setEnabled(true);
		}
		return self::$_profiler;
	}

	/**
	 * Determines whether or not the application is being profiled.
	 *
	 * @return boolean
	 */
	public static function hasProfiling() {
		return self::$_profiling;
	}
}

?>