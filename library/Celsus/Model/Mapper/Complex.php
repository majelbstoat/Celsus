<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Model
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Complex.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Complex mapper for models which rely on multiple bases from the same repository.
 *
 * @category Celsus
 * @package Celsus_Model
 */
class Celsus_Model_Mapper_Complex extends Celsus_Model_Mapper {

	protected $_baseClassPrefix = 'ForceField_Model_Base_';

	/**
	 * The underlying object.
	 *
	 * @var Celsus_Model_Base_Interface
	 */
	protected $_bases = array();

	/**
	 * Maps the business model fields to the underlying base classes and fields.
	 *
	 * @var string
	 */
	protected $_fieldMap = null;

	/**
	 * The relationship between the underlying bases.
	 *
	 * @var array
	 */
	protected $_peerReferences = null;

	public function __construct($service, array $bases = null) {
		$this->_service = $service;

		if (null !== $bases) {
			$this->_bases = $bases;
		}
	}

	/**
	 * Gets a base by name.
	 *
	 * @param string $name
	 * @return Celsus_Model_Base_Interface
	 */
	public function getBase($name) {
		if (!array_key_exists($name, $this->_bases)) {
			$baseClass = $this->_baseClassPrefix . ucfirst($name);
			$this->_bases[$name] = new $baseClass();
		}
		return $this->_bases[$name];
	}

	protected function _getFieldMap() {
		if (null === $this->_fieldMap) {
			foreach ($this->_baseComponents as $baseComponent) {
				$base = $this->getBase($baseComponent);
				$this->_fieldMap[$baseComponent] = $base->getFields();
			}
		}
		return $this->_fieldMap;
	}

	/**
	 * Determines which bases are required for the query, based on the fields supplied.
	 *
	 * @param array $fields
	 * @return array
	 */
	protected function _determineComponents($fields) {
		$fieldMap = $this->_getFieldMap();
		var_dump($fieldMap);
		var_dump($fields);
		$return = array();
		foreach ($fieldMap as $component => $componentFields) {
			$intersectingFields = array_intersect($fields, $componentFields);
			if ($intersectingFields) {
				$return[$component] = $intersectingFields;
			}
		}
		return $return;
	}

	protected function _execute($method, $arguments) {
		$base = $this->getBase($this->_primaryBaseComponent);
		return call_user_func_array(array($base, $method), $arguments);
	}

	protected function _splitResult($result) {
		$fieldMap = $this->_getFieldMap();
		foreach ($fieldMap as $name => $fieldList) {
			$fields = array_flip($fieldList);
			$base = $this->getBase($name);
			$return[] = $base->createRecord(array_intersect_key($result->toArray(), $fields));
		}
		return $return;
	}

	protected function _wrap($data) {
		if ($this->_single) {
			// We are expecting a single row.
			$return = $this->_splitResult($data);
		} else {
			// We are expecting a multi row set.
			foreach ($data as $result) {
				$return[] = $this->_splitResult($result);
			}
		}

		$config = array(
			'data' => $return,
			'mapper' => $this
		);
		$replacement = $this->_single ? 'Model' : 'Model_Set';
		$modelClass = str_replace('Model_Service', $replacement, $this->_service);
		$return = new $modelClass($config);
		return $return;
	}

	protected function _result($data) {
		return count($data) ? true : false;
	}


}