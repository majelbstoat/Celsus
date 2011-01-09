<?php

abstract class Celsus_Form extends Zend_Form {

	/**
	 * The location to redirect to on successful submission of the form.
	 *
	 * @var string
	 */
	protected $_redirect = '/';

	/**
	 * Form level validation clauses.
	 *
	 * @var array
	 */
	protected $_validators = array();

	/**
	 * Namespaces used to prefix validator classnames.
	 *
	 * @var array
	 */
	protected $_validatorNamespaces = array();

	/**
	 * Optional model that this form is generated for.
	 * @var Celsus_Model
	 */
	protected $_model = null;

	/**
	 * Whether this form is for editing an existing object.
	 * @var boolean
	 */
	protected $_editing = false;

	/**
	 * The name of the form
	 * @var string
	 */
	protected $_name = null;

	/**
	 * Adds a hidden id element to the form and sets the method to post.
	 *
	 * @param array $options
	 */
	public function __construct($options = null) {

		parent::__construct($options);

		$this->setValidators();

		$this->addElementPrefixPath('Celsus_Form_Decorator', 'Celsus/Form/Decorator', 'decorator');
		$this->addPrefixPath('Celsus_Form_Decorator', 'Celsus/Form/Decorator', 'decorator');
		$this->addElementPrefixPath('Celsus_Validate', 'Celsus/Validate', 'validate');
		$this->_validatorNamespaces = array('Celsus_Validate');

		$this->setDecorators(array(
	    'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
	    'ClientSideValidated',
	    'ClearedForm'
	  ));

	  // We always want to POST our forms.
	  $this->setMethod(Zend_Form::METHOD_POST);

	  // CSS class for styling.
	  $this->addAttribs(array('class' => 'standard-form'));

	  if (null === $this->_name) {
	  	// If we haven't defined a name, use the lowercased final section of the class name as a default.
	  	$components = explode('_', get_class($this));
	  	$end = end($components);
	  	$this->_name = strtolower($end);
	  }
	  $this->setName($this->_name);

	  // Setup the elements for this form.
	  $this->_setupElements($options);

	  // Add defalt labels to those with none set.
	  $this->_addDefaultLabels();

	  // Avoid the overhead incurred by doing unnecessary translations.
	  $this->_disableElementTranslations();

	  // Adds default filters and decorators to the form elements.
		$this->setElementDecorators(array('Composite'));
		$this->setElementFilters(array('StringTrim'));

	  $redirect = $this->addElement(new Zend_Form_Element_Hidden('redirect'))->getElement('redirect');
	  $redirect->setValue($this->_redirect);

		$submit = $this->addElement(new Zend_Form_Element_Submit('submit'))->getElement('submit');
		$submit->setLabel($this->_getSubmitLabel());
	}

	/**
	 * Disable translations for each element, speeding up particularly setMultiOptions().
	 */
	protected function  _disableElementTranslations() {
		$this->setDisableTranslator(true);
		foreach ($this->getElements() as $element) {
	  	// Avoid the overhead incurred by doing unnecessary translations.
	  	$element->setDisableTranslator(true);
		}
	}

	abstract protected function _setupElements(array $options = null);

	abstract protected function _getSubmitLabel();

	/**
	 * Gets the model associated with this form.
	 *
	 * @return Celsus_Model
	 */
	public function getModel() {
		if (null == $this->_model) {
			throw new Exception("Model not set!");
		}
		return $this->_model;
	}

	/**
	 * Determines whether or not this form is tied to a model.
	 *
	 * @return bool
	 */
	public function hasModel() {
		return (null !== $this->_model);
	}

	/**
	 * Finalises the form elements, by setting up select box options, translating
	 * display elements etc.
	 *
	 * @return unknown_type
	 */
	public function prepareFormElements() {
		$this->setMultiOptions();
		$this->setDisplayValues();
	}

	/**
	 * Set form state from options array
	 *
	 * @param  array $options
	 * @return Celsus_Form
	 */
	public function setOptions(array $options) {
		if (isset($options['model'])) {
			$this->_model = $options['model'];
			unset($options['model']);
		}

		if (isset($options['editing'])) {
			$this->_editing = $options['editing'];
			unset($options['editing']);
		}

		return parent::setOptions($options);
	}

	/**
	 * Returns an array of element names in this form.
	 *
	 * @return array
	 */
	public function getFields() {
		return array_keys($this->_elements);
	}

	/**
	 * Determines whether this form has any generated elements.
	 *
	 * @return bool
	 */
	public function hasGenerated() {
		foreach ($this->_elements as $element) {
			if ($element instanceof Celsus_Form_Element_Generated) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Augments individual validation with form-level conditional validation.
	 *
	 * @param array $data
	 */
	public function isValid($data) {

		if ($this->hasModel()) {
			foreach ($this->getModel()->getValidators() as $validator) {
				if (isset($validator['situations'])) {
					foreach ($validator['situations'] as $field => $situation) {
						$value = $data[$field];
						$validatorBase = $situation[0];
						$validatorArgs = (count($situation) > 1) ? $situation[1] : array();
						if (!Zend_Validate::is($value, $validatorBase, $validatorArgs, $this->_validatorNamespaces)) {
							// There is a situation for this validation and it isn't satisfied, so ignore the conditions.
							continue 2;
						}
					}
				}

				foreach ($validator['conditions'] as $field => $condition) {
					$element = $this->getElement($field);
					$validatorBase = $condition[0];
					$validatorArgs = (count($condition) > 1) ? $condition[1] : array();
					if ('NotEmpty' == $validatorBase) {
						$element->setRequired(true);
					} else {
						$element->addValidator($validatorBase, true, $validatorArgs);
					}
				}
			}
		}
		return parent::isValid($data);
	}

	/**
	 * Sets the validators from the associated model.  The model takes care
	 * of checking whether the validators are well-formed, so we just take
	 * what it has to offer.
	 *
	 * @param array $validators
	 */
	public function setValidators() {
		$this->_validators = $this->hasModel() ? $this->getModel()->getValidators() : array();
	}

	/**
	 * Gets the validators associated with this form.
	 *
	 * @return array
	 */
	public function getValidators() {
		return $this->_validators;
	}

	/**
	 * Returns an array of defined validator namespaces for this form.
	 *
	 * @return array
	 */
	public function getValidatorNamespaces() {
		return $this->_validatorNamespaces;
	}

	/**
	 * Sets the location to redirect to on form's successful submission.
	 * Also sets the form's redirect element, if it exists.
	 *
	 * @param string $redirect Where to redirect to.
	 */
	public function setRedirect($redirect) {
		$this->_redirect = $redirect;
		$this->getElement('redirect')->setValue($redirect);
	}

	/**
	 * Gets the location to redirect to on form's successful submission.
	 *
	 * @return string
	 */
	public function getRedirect() {
		return $this->_redirect;
	}

	/**
	 * Adds default labels to those that have one set for this model (if one is registered) and don't already have a label.
	 *
	 * @return boolean
	 */
	protected function _addDefaultLabels() {
		if (!$this->hasModel()) {
			return false;
		}

		$titles = $this->_model->getColumnTitles();

		foreach ($this->getElements() as $name => $element) {
			if (!$element->getLabel() && array_key_exists($name, $titles)) {
				$element->setLabel($titles[$name]);
			}
		}
	}

	/**
	 * Sets up multi-options for this form.
	 */
	public function setMultiOptions() {
		$this->_model->getMapper()->setMultiOptions($this);
	}

	/**
	 * Determines whether we are editing or not.
	 *
	 * @return bool
	 */
	public function isEditing() {
		return $this->_editing;
	}

    /**
     * Filter a name to only allow valid variable characters
     *
     * @param  string $value
     * @param  bool $allowBrackets
     * @return string
     */
    public function filterName($value, $allowBrackets = false)
    {
        $charset = '^a-zA-Z0-9_\x7f-\xff-';
        if ($allowBrackets) {
            $charset .= '\[\]';
        }
        return preg_replace('/[' . $charset . ']/', '', (string) $value);
    }
}

?>