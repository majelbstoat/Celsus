<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Model
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Simple.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Wraps underlying results in the correct model and caches method calls.
 *
 * @category Celsus
 * @package Celsus_Model
 */
class Celsus_Model_Mapper_Simple extends Celsus_Model_Mapper {

	/**
	 * The underlying object.
	 *
	 * @var Celsus_Model_Base_Interface
	 */
	protected $_base = null;

	/**
	 * The class of the base.
	 *
	 * @var string
	 */
	protected $_baseClass = null;

	public function __construct($service, Celsus_Model_Base_Interface $base = null) {
		$this->_service = $service;

		if (null === $base) {
			$this->_baseClass = str_replace('Model_Service', 'Model_Base', $this->_service);
		} else {
			$this->_base = $base;
		}
	}

	/**
	 * Sets the base object to use for retrieval.
	 *
	 * @return Celsus_Model_Mapper_Simple
	 */
	public function setBase($base = null) {

		if (is_null($base)) {
			$base = $this->_baseClass;
		}

		if (is_string($base)) {
			$config = array('service' => $this->_service);
			$base = new $base($config);
		}

		if (!$base instanceof Celsus_Model_Base_Interface) {
			$class = get_class($base);
			throw new Celsus_Exception("Model base '$class' must implement Celsus_Model_Base_Interface");
		}

		$this->_base = $base;
		return $this;
	}

	/**
	 * Enables lazy loading of the underlying object.
	 *
	 * @return Celsus_Model_Base_Interface
	 */
	public function getBase() {
		if (null === $this->_base) {
			$this->setBase();
		}
		return $this->_base;
	}

	protected function _execute($method, $arguments) {
		$base = $this->getBase();
		return call_user_func_array(array($base, $method), $arguments);
	}

	protected function _result($data) {
		return ($data) ? true : false;
	}

	/**
	 * Given a model that has been constructed from a data source, augments the model
	 * with generated fields that are not persisted.
	 *
	 * @param array $data
	 * @return array
	 */
	protected function _attachGeneratedFields(Celsus_Model $model) {
		$service = $this->_service;
		$fieldData = $service::getFields();
		foreach ($fieldData as $field => $definition) {
			if (Celsus_Model_Service::FIELD_TYPE_GENERATED == $definition['type']) {
				$method = 'attach' . ucfirst($field);
				call_user_func(array($this, $method), $model);
			}
		}

		// Now fix the data, as dirtiness will have been set by the attaching of fields.
		$model->fixData();
	}

	protected function _wrap($data) {

		// First, ensure that every field for this underlying representation is present.
		$data->augment($this->getBase()->getFields());

		$config = array(
			'data' => $data,
			'mapper' => $this
		);

		if ($this->_single) {
			$modelClass = str_replace('Model_Service', 'Model', $this->_service);
			$return = new $modelClass($config);
			$this->_attachGeneratedFields($return);
		} else {
			$modelClass = str_replace('Model_Service', 'Model_Set', $this->_service);
			$return = new $modelClass($config);
			foreach ($return as $model) {
				$this->_attachGeneratedFields($model);
			}
		}

		return $return;
	}
}
