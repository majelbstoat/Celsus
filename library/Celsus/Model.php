<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Model.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Functionality relating to the manipulation of business entities.
 *
 * @defgroup Celsus_Model Celsus Model
 */

/**
 * Represents a business entity.  Acts as a data transfer object with build in security and validation.
 *
 * @ingroup Celsus_Model
 */
abstract class Celsus_Model extends Celsus_Data_Object {

	/**
	 * The ID of this model instance.
	 *
	 * @var mixed $id
	 */
	public $id;

	/**
	 * The mapper that links the model to the underlying.
	 *
	 * @var Celsus_Model_Mapper
	 */
	protected $_mapper = null;

	/**
	 * A breakdown of which marshal is used for each source.
	 *
	 * @param Celsus_Data_Marshal_Interface
	 */
	protected $_marshalledSources = array();

	/**
	 * A breakdown of which fields are from which source.
	 *
	 * @param array
	 */
	protected $_sourceFieldMap = null;

	/**
	 * Where the data originally came from.
	 *
	 * @param array
	 */
	protected $_sources = null;

	/**
	 * A flag to specify whether the model data is valid.
	 *
	 * @var boolean
	 */
	protected $_valid = null;

	/**
	 * The messages returned by failed validation.
	 *
	 * @param array
	 */
	protected $_validationMessages = array();

	/**
	 * The rules to validate against.
	 *
	 * @param array
	 */
	protected $_validationRules = array();


	/**
	 * Constructs a new Celsus Model.
	 *
	 * @param array $options
	 */
	public function __construct(array $config) {

		// Check that a mapper is specified.
		if (!isset($config['mapper'])) {
			throw new Celsus_Exception("Can't instantiate a model without a mapper.");
		}
		$this->_mapper = $config['mapper'];

		// Check that data was specified.
		if (!isset($config['data'])) {
			throw new Celsus_Exception("Can't instantiate a model instance without data.");
		}
		parent::__construct($config['data']);

	}

	// Validation

	/**
	 * Uses the model service to determine if the data is valid.
	 */
	public function isValid() {
		if (null === $this->_valid) {
			$this->_validationMessages = array();
			$service = $this->_mapper->getService();
			$this->_valid = $service::validate($this);
		}
		return $this->_valid;
	}

	public function setValidationMessages($field, $messages) {
		$this->_validationMessages[$field] = $messages;
	}

	public function getValidationMessages() {
		return $this->_validationMessages;
	}

	// Data Transfer Properties

	public function __set($field, $value) {
		if (parent::__set($field, $value)) {
			$this->_valid = null;
		}
	}

	protected function _setData($data) {
		if (!is_array($data)) {
			// Force information into an array.
			$data = array($data);
		}

		$fieldMap = array_flip($this->_mapper->getFieldMap());
		$marshalled = false;
		foreach ($data as $sourceKey => $item) {
			if (is_object($item)) {
				foreach (parent::$_marshals as $provided => $marshal) {
					if ($item instanceof $provided) {
						// We have a provider that can marshal this object.
						$providedData = call_user_func(array($marshal, 'provide'), $item);
						foreach ($providedData as $key => $value) {
							if (array_key_exists($key, $fieldMap)) {
								$this->_data[$fieldMap[$key]] = $value;
							} else {
								$this->_data[$key] = $value;
							}
							$this->_sourceFieldMap[$sourceKey][] = $key;
						}
						$this->_sources[$sourceKey] = $item;
						$this->_marshalledSources[$sourceKey] = $marshal;
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

	/**
	 * Returns all the data, subject to the current identity's permissions.
	 * Runs recursively over sub-records.
	 *
	 * @return array;
	 */
	public function getData() {
		if (null === $this->_readableFields) {
			$this->_determineReadableFields();
		}
		$return = array_intersect_key($this->_data, array_flip($this->_readableFields));
		foreach ($return as $field => $data) {
			if ($data instanceof self) {
				$return[$field] = $data->getData();
			}
		}
		return $return;
	}


	// Persistence

	/**
	 * @todo Handle complex save operations where split ids need to be set.
	 */
	public function save() {
		$return = false;
		if ($this->id && !$this->_dirty) {
			// No changes have been made to the object
			$return = $this->id;
		} else {
			if ($this->isValid()) {
				$modelFieldMap = $this->_mapper->getFieldMap();
				$fieldMap = array_flip($this->_mapper->getFieldMap());
				foreach ($this->_sourceFieldMap as $sourceKey => $fields) {
					// Build an array of data to save to this underlying, making use of the model field map to
					// convert between business model fields and underlying fields.
					$data = array();
					foreach ($this->_data as $key => $value) {
						if (array_key_exists($key, $modelFieldMap)) {
							$data[$modelFieldMap[$key]] = $value;
						} else {
							$data[$key] = $value;
						}
					}

					// Save the document and store the return ID.
					$id = call_user_func_array(array($this->_marshalledSources[$sourceKey], 'save'), array($data, $this->_sources[$sourceKey]));

					// Now, re-source the data from the underlying entity as triggers might have updated additional data fields in the underlying.
					$providedData = call_user_func(array($this->_marshalledSources[$sourceKey], 'provide'), $this->_sources[$sourceKey]);
					foreach ($providedData as $key => $value) {
						if (array_key_exists($key, $fieldMap)) {
							$this->_data[$fieldMap[$key]] = $value;
						} else {
							$this->_data[$key] = $value;
						}
					}

					if (!$this->id) {
						// @todo consider implications of atomicity.
						$this->id = $id;
						break;
					} elseif (!isset($return)) {
						// Assumes that for complex, multi-part objects, the primary part is going to first.
						$return = $id;
					}
				}
			} else {
				throw new Celsus_Model_Exception_InvalidData("One or more fields were invalid.  Please check your data and try again.");
			}
		}
		return $return;
	}
}