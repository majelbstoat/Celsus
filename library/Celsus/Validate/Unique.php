<?php

class Celsus_Validate_Unique extends Zend_Validate_Abstract {

	const NOT_UNIQUE = 'notUnique';

	/**
	 * The model to check for uniqueness in.
	 * 
	 * @var Celsus_Model
	 */
	protected $_model;
	
	/**
	 * An identifier of the row with which this value clashes, if applicable
	 * 
	 * @var string
	 */
	protected $_clash;

	/**
	 * The name of the model which contains the clash, if applicable
	 * 
	 * @var string
	 */
	protected $_modelName;
	
	
	/**
	 * The model field that is human readable for return messages.
	 * 
	 * @var string
	 */
	protected $_identifier = 'name';
	
	/**
	 * Variable substitution map.
	 * 
	 * @var array
	 */
	protected $_messageVariables = array(
		'clash' => '_clash',
		'model' => '_modelName'
	);
	
	/**
	 * The field to test for uniqueness on.
	 * 
	 * @var string
	 */
	protected $_field;
	
	/**
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::NOT_UNIQUE => "This field must be unique, but '%value%' is already in use in the existing %model%, %clash%",
	);

	/**
	 * Sets validator options.
	 * 
	 * @param Celsus_Model $model
	 */
	public function __construct(Celsus_Model $model, $field, $identifier = null) {
		$this->setModel($model);
		$this->setField($field);
		if (null !== $identifier) {
			$this->setIdentifier($identifier);
		}
	}
	
	/**
	 * Sets the model to validate against.
	 * 
	 * @param Celsus_Model $model
	 */
	public function setModel(Celsus_Model $model) {
		$this->_model = $model;
	}
	
	/**
	 * Sets the identifier for generating a clash message, if necessary.
	 * 
	 * @param string
	 */
	public function setIdentifier($identifier) {
		$this->_identifier = $identifier;
	}

	/**
	 * Sets the field for determining uniqueness.
	 * 
	 * @param string
	 */
	public function setField($field) {
		$this->_field = $field;
	}	
	
	/**
	 * Defined by Zend_Validate_Interface
	 *
	 * Returns true if and only if $value is unique in the specified table.
	 *
	 * @param mixed $value
	 * @param array $context
	 * @return boolean
	 */
	public function isValid($value, $context = null) {
		if (!$context) {
			throw new Celsus_Exception("Cannot test for uniqueness with no context");
		}
		
		if (null === $this->_model) {
			throw new Celsus_Exception("No model specified for uniqueness check.");
		}

		if (null === $this->_field) {
			throw new Celsus_Exception("No field specified for uniqueness check.");
		}		
		
		$valueString = (string) $value;
		$this->_setValue($valueString);

		$select = $this->_model->select()->where("$this->_field = ?", $valueString);
		if (isset($context['id'])) {
			$select = $select->where('id != ?', $context['id']);
		}
		
		if ($clash = $this->_model->fetchRow($select)) {
			$this->_clash = $clash->{$this->_identifier};
			$this->_modelName = $this->_model->getTitle();
			$this->_error(self::NOT_UNIQUE);
			return false;
		}
						
		return true;
	}

	/**
	 * Returns code snippet used for client-side validation.
	 * 
	 * @param string $name The name of the element.
	 * @return boolean No client-side validation for unique constraint.
	 */
	public function getClientSideValidation($name) {
		return false;
	}	
	
}
