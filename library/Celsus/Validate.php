<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Validate.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Validation functionality.
 *
 * @defgroup Celsus_Validation Celsus Validation
 */

/**
 * Provides static validation functionality.
 *
 * @ingroup Celsus_Validation
 */
class Celsus_Validate extends Zend_Validate {

	static $_validationMessages = null;

	/**
	 * @param  mixed    $value
	 * @param  string   $classBaseName
	 * @param  array    $args          OPTIONAL
	 * @param  mixed    $namespaces    OPTIONAL
	 * @return boolean
	 * @throws Zend_Validate_Exception
	 */
	public static function is($value, $classBaseName, array $args = array(), $namespaces = array()) {
		static::$_validationMessages = array();
		$namespaces = array_merge((array) $namespaces, self::$_defaultNamespaces, array('Zend_Validate'));
		$className  = ucfirst($classBaseName);
		try {
			if (!class_exists($className, false)) {
				require_once 'Zend/Loader.php';
				foreach($namespaces as $namespace) {
					$class = $namespace . '_' . $className;
					$file  = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
					if (Zend_Loader::isReadable($file)) {
						Zend_Loader::loadClass($class);
						$className = $class;
						break;
					}
				}
			}

			$class = new ReflectionClass($className);
			if ($class->implementsInterface('Zend_Validate_Interface')) {
				if ($class->hasMethod('__construct')) {
					$keys    = array_keys($args);
					$numeric = false;
					foreach($keys as $key) {
						if (is_numeric($key)) {
							$numeric = true;
							break;
						}
					}

					if ($numeric) {
						$object = $class->newInstanceArgs($args);
					} else {
						$object = $class->newInstance($args);
					}
				} else {
					$object = $class->newInstance();
				}

				if ($object->isValid($value)) {
					return true;
				} else {
					static::$_validationMessages = $object->getMessages();
					return false;
				}
			}
		} catch (Zend_Validate_Exception $ze) {
			// if there is an exception while validating throw it
			throw $ze;
		} catch (Exception $e) {
			// fallthrough and continue for missing validation classes
		}
	}

	public static function getValidationMessages() {
		return static::$_validationMessages;
	}

}