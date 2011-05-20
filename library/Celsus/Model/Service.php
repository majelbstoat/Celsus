<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Model
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Service.php 72 2010-09-14 01:56:33Z jamie $
 */

/**
 * Celsus model base class.
 *
 * @category Celsus
 * @package Celsus_Model
 */
abstract class Celsus_Model_Service implements Celsus_Model_Service_Interface {

	const FIELD_TYPE_STRING = 'string';
	const FIELD_TYPE_NUMBER = 'number';
	const FIELD_TYPE_BOOLEAN = 'boolean';
	const FIELD_TYPE_DATE = 'date';
	const FIELD_TYPE_TIME = 'time';
	const FIELD_TYPE_DATETIME = 'datetime';
	const FIELD_TYPE_PASSWORD = 'password';
	const FIELD_TYPE_REFERENCE = 'reference';
	const FIELD_TYPE_PARENT_REFERENCE = 'parentReference';

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

	protected static $_validationMessages = array();

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
			$validators = static::_getDefaultValidators();
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

	/**
	 * Given the supplied model, determines which constraints have to be applied
	 * to which fields.
	 * @param $data
	 * @return array
	 */
	protected static function _getValidationRules(Celsus_Model $model) {
		$validationRules = array();
		foreach (static::getValidators() as $validator) {
			if (isset($validator['situations'])) {
				foreach ($validator['situations'] as $field => $situations) {
					if (!is_array($situations)) {
						$situations = array($situations);
					}
					foreach ($situations as $situation) {
						$value = $model->$field;
						if (is_array($situation)) {
							$validatorBase = $situation[0];
							$validatorArgs = array(
							$situation[1]
							);
						} else {
							$validatorBase = $situation;
							$validatorArgs = array();
						}
						if (!Zend_Validate::is($value, $validatorBase, $validatorArgs, self::$_validatorNamespaces)) {
							// There is a situation for this validation and it isn't satisfied, so ignore the conditions.
							continue 3;
						}
					}
				}
			}

			foreach ($validator['conditions'] as $field => $condition) {
				if (!is_array($condition)) {
					$condition = array($condition);
				}
				$validatorBase = $condition[0];
				$validatorArgs = (count($condition) > 1) ? $condition[1] : array();
				$validationRules[$field] = array($validatorBase, $validatorArgs);
			}
		}
		return $validationRules;
	}

	public static function validate(Celsus_Model $model) {
		$validationRules = self::_getValidationRules($model);
		$result = true;
		foreach ($validationRules as $field => $rule) {
			$value = $model->$field;
			if (!Celsus_Validate::is($value, $rule[0], array($rule[1]), self::$_validatorNamespaces)) {
				$model->setValidationMessages($field, Celsus_Validate::getValidationMessages());
				$result = false;
			}
		}
		return $result;
	}

	public static function getValidationMessages() {
		return static::$_validationMessages;
	}

	// Model Template

	/**
	 * Sets default values.
	 */
	protected static function _setupDefaultValues() {
		static::$_defaultValues = array();
	}

	public static function getTitle() {
		if (null === static::$_title) {
			$classnameComponents = explode('_', get_called_class());
			$title = array_pop($classnameComponents);
			static::$_title = $title;
		}
		return static::$_title;
	}

	public static function getFields() {
		return static::$_defaultFields;
	}

	public static function getFieldMetadata() {
		$fields = static::$_defaultFields;
		foreach ($fields as $field => $definition) {
			foreach ($definition as $key => $value) {
				$return[$key][$field] = $value;
			}
		}
		return $return;
	}

	public static function getReferencedFields() {
		$return = array();
		$metadata = self::getFieldMetadata();
		foreach ($metadata['type'] as $name => $type) {
			if (Celsus_Model_Service::FIELD_TYPE_REFERENCE == $type) {
				$return[$name] = $metadata['reference'][$name];
			}
		}
		return $return;
	}

	public static function getParentReferencedFields() {
		$return = array();
		$metadata = self::getFieldMetadata();
		foreach ($metadata['type'] as $name => $type) {
			if (Celsus_Model_Service::FIELD_TYPE_PARENT_REFERENCE == $type) {
				$return[$name] = $metadata['reference'][$name];
			}
		}
		return $return;
	}

	/**
	 * Returns the standard URL collection base for this service.
	 *
	 * @return string
	 */
	public static function getPrimaryLocation() {
		return static::$_primaryLocation;
	}

	/**
	 * Execute the base find
	 *
	 * @param mixed $identifer
	 */
	public static function find($identifier) {
		return self::_underlying()->multiple()->find($identifier);
	}

	public static function getDefaultValues() {
		return static::$_defaultValues ?: array();
	}

	/**
	 * Fetches data from the underlying base, given the specified data.
	 *
	 * @param mixed $identifier
	 * @return Celsus_Model
	 * @throws Celsus_Exception When the supplied $identifier is invalid.
	 */
	public static function fetchOrCreateRecord($identifier) {
		if ($identifier) {
			$records = self::_underlying()->multiple()->find($identifier);
			// Find always retrieves multiple records, even if there is only one result.
			$record = $records[0];
			if (!$record) {
				// Identifier was invalid.
				throw new Celsus_Model_Exception_NotFound("Invalid identifier");
			}
		} else {
			$record = self::_underlying()->single()->createRecord(self::getDefaultValues());
		}
		return $record;
	}

	/**
	 * Fetches multiple records based on supplied criteria.
	 */
	public static function fetchAll() {
		return self::_underlying()->multiple()->fetchAll(func_get_args());
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

	/**
	 * Given a model, a model id or an array containing model data, returns its descriptive name.
	 *
	 * @param Celsus_Model|array|string $model
	 */
	public static function getDescription($data) {
		if (!is_array($data)) {
			if (!$data instanceof Celsus_Model) {
				// Assume it's an id.
				$id = $data;
				$models = static::find($id);
				$data = $models[0];
			}
			$data = $data->toArray();
		}
		$descriptiveFields = is_array(static::$_descriptiveField) ? static::$_descriptiveField : array(static::$_descriptiveField);
		foreach($descriptiveFields as $descriptiveField) {
			$return[] = $data[$descriptiveField];
		}
		return implode(" ", $return);
	}

	/**
	 * Retuns lookup values in a format suitable for populating a select box.
	 *
	 * @return array
	 */
	public static function getLookupValues($options = null) {
		$data = static::_getLookupData($options);
		$fields = (is_array(static::$_descriptiveField)) ? static::$_descriptiveField : array(static::$_descriptiveField);
		$return = array();

		foreach ($data as $item) {
			$components = array();
			foreach ($fields as $component) {
				$components[] = $item->$component;
			}
			$return[$item->id] = implode(' ', $components);
		}
		return $return;
	}

	protected static function _getLookupData($options = null) {
		throw new Celsus_Exception(__FUNCTION__ . ' must be overridden in ' . get_called_class());
	}

	// Caching

	/**
	 * Returns the caching and wrapping decorator to intercept expensive method calls.
	 *
	 * @return Celsus_Model_Service_Decorator_Simple
	 */
	protected static function _underlying() {
		if (null === static::$_underlying) {
			if (Celsus_Model_Mapper::MAPPER_TYPE_SIMPLE == static::$_mapperType) {
				$mapperClass = 'Celsus_Model_Mapper_' . static::$_mapperType;
			} else {
				$mapperClass = str_replace('Model_Service', 'Model_Mapper', get_called_class());
			}
			static::$_underlying = new $mapperClass(get_called_class());
		}
		return static::$_underlying;
	}

}