<?php

class Celsus_View_Helper_Broker {

	/**
	 * A cache of all the helper objects used to render the template.
	 *
	 * @var array $_helpers
	 */
	protected static $_helpers = array();

	protected static $_prefixes = array(
		'Celsus'
	);

	/**
	 * Returns a view helper that can be used to render content.
	 *
	 * @param string $name
	 * @return Celsus_View_Helper
	 * @throws Celsus_Exception
	 */
	public static function getHelper($name) {
		if (!isset(self::$_helpers[$name])) {
			$suffix = ucfirst($name);
			foreach (self::$_prefixes as $prefix) {
				$className = "{$prefix}_View_Helper_{$suffix}";

				// Try to load this class.
				$classFile = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
				if (Zend_Loader::isReadable($classFile)) {
					if (class_exists($className)) {

						// Create a new helper object and return it.
						self::$_helpers[$name] = new $className();
						return self::$_helpers[$name];
					}
				}
			}

			// The helper was not found.
			throw new Celsus_Exception("Helper $name not found", Celsus_Http::INTERNAL_SERVER_ERROR);
		}

		// Found from cache.
		return self::$_helpers[$name];
	}

	public static function addPrefix($prefix) {
		array_unshift(self::$_prefixes, $prefix);
	}
}