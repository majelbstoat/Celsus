<?php

/**
 * Representation of a business entity.  Acts as a data transfer object with build in security and validation.
 *
 * @author majelbstoat
 *
 */
abstract class Celsus_Model extends Celsus_Data_Object implements Zend_Validate_Interface {

	/**
	 * A breakdown of which fields are from which source.
	 *
	 * @param array
	 */
	protected $_marshalledFields = null;

	/**
	 * Where the data originally came from.
	 *
	 * @param array
	 */
	protected $_sources = null;

	/**
	 * The service that describes this model.
	 *
	 * @param Celsus_Model_Service_Interface
	 */
	protected $_service = null;

	/**
	 * The messages returned by failed validation.
	 *
	 * @param array
	 */
	protected $_validationMessages = array();

	/**
	 * Constructs a new Celsus Model.
	 *
	 * @param array $options
	 */
	public function __construct(array $config) {

		// Check that a service is specified.
		if (!isset($config['service'])) {
			throw new Celsus_Exception("Can't instantiate a model without a service definition");
		}
		$this->_service = $config['service'];

		// Check that data was specified.
		if (!isset($config['data'])) {
			throw new Celsus_Exception("Can't instantiate a model instance without data");
		}
		parent::__construct($config['data']);

	}

	/**
	 * Tests to see if the supplied data is valid for this model.
	 * Required by Zend_Validate_Interface
	 *
	 * @todo Allow this to handle partial updates, where only a subset of the
	 * full required data is supplied.
	 * @param mixed $data
	 * @return bool
	 */
	public function isValid($data) {
		$this->_prepareValidators($data);
		$result = true;
		foreach ($this->_validationRules as $field => $rule) {
			$value = $data->$field;
			if (!Zend_Validate::is($value, $rule[0], array(
			$rule[1]
			), $this->_validatorNamespaces)) {
				$this->_validationMessages = Celsus_Validate::getValidateMessages();
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * Given the set of data, determines which constraints have to be applied
	 * to which fields.
	 * @param $data
	 * @return unknown_type
	 */
	protected function _prepareValidators($data) {
		foreach ($this->getValidators() as $validator) {
			if (isset($validator['situations'])) {
				foreach ($validator['situations'] as $field => $situations) {
					foreach ($situations as $situation) {
						$value = $data->$field;
						if (is_array($situation)) {
							$validatorBase = $situation[0];
							$validatorArgs = array(
							$situation[1]
							);
						} else {
							$validatorBase = $situation;
							$validatorArgs = array();
						}
						if (!Zend_Validate::is($value, $validatorBase, $validatorArgs, $this->_validatorNamespaces)) {
							// There is a situation for this validation and it isn't satisfied, so ignore the conditions.
							continue 3;
						}
					}
				}
			}

			foreach ($validator['conditions'] as $field => $condition) {
				$validatorBase = $condition[0];
				$validatorArgs = (count($condition) > 1) ? $condition[1] : array();
				if ('NotEmpty' == $validatorBase) {
					//$element->setRequired(true);
				} else {
					$this->_validationRules[$field] = array(
					$validatorBase,
					true,
					$validatorArgs
					);
				}
			}
		}
	}

	/**
	 * Returns the messages given when a model fails validation.
	 *
	 * @return array
	 */
	public function getMessages() {
		return $this->_validationMessages;
	}

	/**
	 * Gets the human-readable title of this model.
	 *
	 * @return string
	 */
	public function getTitle() {
		if (is_null($this->_title)) {
			// Generate a default title, based on the class name.
			$class = get_class($this);
			$this->_title = substr($class, strrpos($class, '_') + 1);
		}
		return $this->_title;
	}

	/**
	 * Gets the default values for a new row.
	 */
	public function getDefaultValues() {
		if (is_null($this->_defaultValues)) {
			$this->_setupDefaultValues();
		}
		return $this->_defaultValues;
	}

	/**
	 * Returns the default fields for this model.
	 *
	 * @return array
	 */
	public function getDefaultFields() {
		return array_keys($this->_defaultFields);
	}

	// Data Transfer Properties

	protected function _setData($data) {
		if (!is_array($data)) {
			// Force information into an array.
			$data = array($data);
		}

		$marshalled = false;
		foreach ($data as $sourceKey => $item) {
			if (is_object($item)) {
				foreach (parent::$_marshals as $provided => $marshal) {
					if ($item instanceof $provided) {
						// We have a provider that can marshal this object.
						$providedData = call_user_func(array($marshal, 'provide'), $item);
						foreach ($providedData as $key => $value) {
							$this->_data[$key] = $value;
							$this->_marshalledFields[$marshal][] = $key;
						}
						$this->_sources[$marshal] = $item;
						$marshalled = true;
						break;
					}
				}
				if (!$marshalled) {
					throw new Celsus_Exception("Data must come from an object that can be marshalled: " . get_class($item));
				}
			}
		}

		$this->_readableFields = null;
		$this->_writeableFields = null;
		return $this;
	}


	// Persistence


	/**
	 * Short circuit this when the object is not dirty.
	 */
	public function save() {

		if (!$this->_dirty) {
			// Or a better return type.
			return true;
		}

		// @todo Needs to be rewritten to take account of all the marshals that provide different fields.

		return call_user_func_array(array($this->_marshal, 'save'), array($this->_data, $this->_source));
	}
}