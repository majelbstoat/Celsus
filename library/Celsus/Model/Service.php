<?php
abstract class Celsus_Model_Service implements Celsus_Model_Service_Interface {

	/**
	 * Used for caching results.
	 *
	 * @var Celsus_Model_Service_Decorator_Simple
	 */
	protected static $_underlying = null;

	/**
	 * The human-readable field that uniquely identifies the row.
	 *
	 * @var string
	 */
	protected static $_descriptiveField = null;

	/**
	 * Human-readable titles used for labels and column headings.
	 */
	protected static $_defaultFields = null;

	/**
	 * The default values for a new row.
	 *
	 * @var array
	 */
	protected static $_defaultValues = null;

	/**
	 * The mapper type for the service.
	 *
	 * @var string
	 */
	protected static $_mapperType = Celsus_Model_Mapper::MAPPER_TYPE_SIMPLE;

	/**
	 * The resource id of this model for ACL purposes.
   *
	 * @param string
	 */
	protected static $_resourceId = null;

	/**
	 * The human-readable title of this model.
	 *
	 * @var string
	 */
	protected static $_title = null;

	/**
	 * The validators used to validate this model.
	 *
	 * @var array
	 */
	protected static $_validators = null;

	/**
	 * The namespaces that we will use to validate data.
	 *
	 * @var array
	 */
	protected static $_validatorNamespaces = array(
		'Celsus_Validate'
	);

	/**
	 * The rules to validate with.
	 *
	 * @param array
	 */
	protected static $_validationRules = array();

	// ACL

	/**
	 * Returns the resource Id of this model, for ACL.
	 *
	 * @return string
	 */
	protected static function getResourceId() {
		return static::$_resourceId;
	}

	// Validation

	/**
	 * Adds conditional validation clause.
	 *
	 * @param array $validator
	 */
	public static function addValidator(array $validator) {
		if (!isset($validator['conditions'])) {
			throw new Celsus_Exception("Must specify at least one condition");
		}

		if (!is_array($validator['conditions'])) {
			throw new Celsus_Exception("Conditions must be specified as an array");
		}

		if (isset($validator['situations']) && !is_array($validator['situations'])) {
			throw new Celsus_Exception("Situations must be specified as an array");
		}
		static::$_validators[] = $validator;
	}

	/**
	 * Adds validation, in addition to existing clauses.
	 *
	 * @param array $validators
	 */
	public static function addValidators(array $validators) {
		foreach ($validators as $validator) {
			self::addValidator($validator);
		}
	}

	/**
	 * Returns the validators used to validate this model.
	 *
	 * @return array
	 */
	public static function getValidators() {
		if (is_null(static::$_validators)) {
			$validators = self::_getDefaultValidators();
			self::setValidators($validators);
		}
		return static::$_validators;
	}

	/**
	 * Sets validation, replacing all existing clauses.
	 *
	 * @param array $validators
	 */
	public static function setValidators(array $validators) {
		static::$_validators = array();
		foreach ($validators as $validator) {
			self::addValidator($validator);
		}
	}

	protected static function _getDefaultValidators() {
		return array();
	}

	// Model Template

	/**
	 * Sets default values.
	 */
	protected static function _setupDefaultValues() {
		static::$_defaultValues = array();
	}

	/**
	 * Execute the base find
	 *
	 * @param mixed $identifer
	 */
	public static function find($identifier) {
		return self::_underlying()->find($identifier);
	}

	/**
	 * Fetches data from the underlying base, given the specified data.
	 *
	 * @param mixed $identifier
	 * @return Celsus_Data_Object
	 * @throws Exception When the supplied $identifier is invalid.
	 */
	public static function fetchOrCreateRecord($identifier) {
		return ($identifier) ? self::_underlying()->find($identifier) : self::_underlying()->createRecord(self::getDefaultValues());
	}

	/**
	 * Fetches multiple records based on supplied criteria.
	 */
	public static function fetchAll() {
		return self::_underlying()->fetchAll(func_get_args());
	}

	/**
	 * Execute the base delete
	 *
	 * @param string $identifier
	 */
	public static function delete($identifier, $params = array()) {
		return self::_underlying()->delete($identifier, $params);
	}

	/**
	 * Sets the base(s) to be used for this service via the mapper.  Enables mocking.
	 *
	 * @param string|array|Celsus_Model_Base_Interface $base
	 */
	public static function setBase($base) {
		static::_underlying()->setBase($base);
	}

	// Caching

	/**
	 * Returns the caching and wrapping decorator to intercept expensive method calls.
	 *
	 * @return Celsus_Model_Service_Decorator_Simple
	 */
	protected static function _underlying() {
		if (null === static::$_underlying) {
			if (Celsus_Model_Mapper::MAPPER_TYPE_CUSTOM == static::$_mapperType) {
				$mapperClass = str_replace('Model_Service', 'Model_Mapper', get_called_class());
			} else {
				$mapperClass = 'Celsus_Model_Mapper_' . static::$_mapperType;
			}
			static::$_underlying = new $mapperClass(get_called_class());
		}
		return static::$_underlying;
	}

}