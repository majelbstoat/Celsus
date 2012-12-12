<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Db.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Dabatase functionality
 *
 * @defgroup Celsus_Db Celsus Database
 */

/**
 * Database connection broker, with profiling and logging support.
 *
 * @ingroup Celsus_Db
 */
class Celsus_Db {

	/**
	 * A collection of the databases in use.
	 *
	 * @var array
	 */
	protected static $_databases = array();

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

	/**
	 * Database configuration.
	 *
	 * @var Zend_Config $_config
	 */
	protected static $_config = null;

	public static function setConfig(Zend_Config $config) {
		self::$_config = $config;
	}

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
	 * Returns the application's default adapter.
	 * @return mixed
	 */
	public static function getDefaultAdapter() {
		return self::getAdapter(self::getDefaultAdapterName());
	}

	/**
	 * Instantiates a database adapter handle.
	 *
	 * @param string $name The internal name of the database to connect to.
	 * @return mixed
	 */
	public static function getAdapter($name) {
		if (!isset(self::$_databases[$name])) {
			$config = self::$_config->database->$name;
			if (null == $config) {
				throw new Celsus_Exception("$name is not a valid database to connect to.");
			}
			$connection = $config->connection->toArray();

			$type = $config->type ? $config->type : 'relational';
			$factory = 'Celsus_Db_Engine_' . ucfirst($type);
			if (!class_exists($factory, true)) {
				throw new Celsus_Exception($type . " is not a valid database type");
			}

			self::$_databases[$name] = call_user_func_array(array($factory, 'factory'), array($config->engine, $connection));
		}
		return self::$_databases[$name];
	}

	public static function getEngineType($name) {
		$config = self::$_config->database->$name;
		return $config->engine;
	}

	/**
	 * Returns an array of the database adapters that have been loaded so far.
	 *
	 * @return array
	 */
	public static function getLoadedAdapters() {
		return array_keys(self::$_databases);
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