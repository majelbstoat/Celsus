<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Form.php 72M 2010-09-20 04:59:47Z (local) $
 */

/**
 * Form functionality
 *
 * @defgroup Celsus_Form Celsus Form
 */

/**
 * Defines standard form functionality and provides methods for interacting with
 * elements, subforms and related services.
 *
 * @ingroup Celsus_Form
 */
abstract class Celsus_Form extends ZendX_JQuery_Form {

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
	 * Optional default service that this form is generated for.
	 * @var Celsus_Model_Service
	 */
	protected $_service = null;

	protected $_additionalValidation = array();

	/**
	 * Whether this form is for editing an existing object.
	 * @var boolean
	 */
	protected $_editing = false;

	/**
	 * The internal name of the form
	 * @var string
	 */
	protected $_internalName = null;

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

		// As browsers don't support HTTP PUT yet, we always want to POST.
		$this->setMethod(Zend_Form::METHOD_POST);

		// CSS class for styling.
		$this->addAttribs(array('class' => "standard-form formerize validate"));

		if (null === $this->_internalName) {
	    // If we haven't defined a name, use the lowercased final section of the class name as a default.
	    $components = explode('_', get_class($this));
	    $end = end($components);
	    $this->_internalName = strtolower($end);
    }
    $this->setName($this->_internalName . '-form');

    // Setup the elements for this form.
    $this->_setupElements($options);

    // Add defalt labels and other metadata to those with none set.
    $this->_addMetadata();

    // Avoid the overhead incurred by doing unnecessary translations.
    $this->_disableElementTranslations();

    $this->setMultiOptions();

    // Adds default filters and decorators to the form elements.
    //$this->setElementDecorators(array('Composite'));
    $this->setElementFilters(array('StringTrim'));

		$this->setDecorators(array(
			'FormElements',
			'ClientSideValidated',
			'ClearedForm'
		));

		// Add elements to a default display group if there isn't already one.
		if (!$this->getDisplayGroup('main')) {
			$this->addDisplayGroup($this->getElementNames(), 'main', array(
				'order' => 0,
				'legend' => $this->_getMainLegend(),
			));
		}

	  // Add default elements.
		$redirect = $this->addElement(new Zend_Form_Element_Hidden('redirect'))->getElement('redirect');
		$redirect->setValue($this->_redirect)
			->setDecorators(array());

		$submit = $this->addElement(new Zend_Form_Element_Submit('submit'))->getElement('submit');
		$submit->setLabel($this->_getSubmitLabel());

		$this->addDisplayGroup(array('redirect', 'submit'), 'action', array(
			'legend' => 'Action'
		));

		$this->setDisplayGroupDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			'FieldSet',
		));
	}

	public function addSubFormFromForm(Celsus_Form $form, $name, $title, array $additionalElements = array()) {
		$excludedElements = array('submit', 'redirect', 'id');

		$this->_additionalValidation[$name] = $form->getInternalName();
		$elements = array_diff_key($form->getElements(), array_flip($excludedElements));
		$subForm = new Zend_Form_SubForm();
		$subForm->addElements(array_merge($additionalElements, $elements));
		$subForm->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			array('FieldSet', array('legend' => $title)),
		));

		$this->addSubForm($subForm, $name);
	}

	public function getAdditionalValidation() {
		return $this->_additionalValidation;
	}

	protected function _getMainLegend() {
		$service = $this->getService();
		return $service::getTitle() . ' Details';
	}

	public function getInternalName() {
		return $this->_internalName;
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

	public function getElementNames() {
		return array_keys($this->_elements);
	}

	/**
	 * Default element setup generates a form automatically from the
	 * associated service, if possible.
	 *
	 * @param array $options
	 */
	protected function _setupElements(array $options = null) {
		if ($this->hasService()) {
			$this->_setElementsFromService($this->_service);
		}
	}

	protected function _setElementsFromService($service) {
		$fields = $service::getFields();

		$this->addElement(new Celsus_Form_Element_Generated('id'));
		foreach ($fields as $field => $definition) {
			switch ($definition['type']) {

				case Celsus_Model_Service::FIELD_TYPE_STRING:
				case Celsus_Model_Service::FIELD_TYPE_NUMBER:
				case Celsus_Model_Service::FIELD_TYPE_DATE:
					$element = $this->addElement(new Zend_Form_Element_Text($field))->getElement($field);
					break;

				case Celsus_Model_Service::FIELD_TYPE_REFERENCE:
					$element = $this->addElement(new Celsus_Form_Element_NullSelect($field))->getElement($field);
					break;

				case Celsus_Model_Service::FIELD_TYPE_PARENT_REFERENCE:
					$element = $this->addElement(new Celsus_Form_Element_Display($field))->getElement($field);
					$element->addDecorator('LookupReference', array('service' => $definition['reference']))->addDecorator('Output');
					break;

			}

			// Now add filters.

			// Now add validators.
		}

	}

	/**
	 * Populates data from references.
	 */
	public function populate(array $values) {
		// First, do standard population.
		parent::populate($values);

		// Now populate from references.
		$references = $this->_getReferencedFields();
		foreach (array_keys($references) as $field) {
			if (is_array($values[$field])) {
				$this->getElement($field)->setValue($values[$field]['id']);
			}
		}
	}

	protected function _getSubmitLabel() {
    $components = explode('_', get_class($this));
    $end = end($components);
		return $this->_editing ? "Update $end" : "Add $end";
	}

	/**
	 * Gets the service associated with this form.
	 *
	 * @return Celsus_Model_Service
	 */
	public function getService() {
		return $this->_service;
	}

	/**
	 * Determines whether or not this form is tied to a model.
	 *
	 * @return bool
	 */
	public function hasService() {
		return (null !== $this->_service);
	}

	/**
	 * Set form state from options array
	 *
	 * @param  array $options
	 * @return Celsus_Form
	 */
	public function setOptions(array $options) {
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
	 * Sets the validators from the associated model service.  The service takes care
	 * of checking whether the validators are well-formed, so we just take
	 * what it has to offer.
	 *
	 * @param array $validators
	 */
	public function setValidators() {
		$service = $this->_service;
		$this->_validators = $this->hasService() ? $service::getValidators() : array();
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
	 * Adds default labels to those that have one set for this service (if one is registered) and don't already have a label.
	 *
	 * @return boolean
	 */
	protected function _addMetadata() {
		$metadata = $this->_getServiceMetadata();
		if (!$metadata) {
			return false;
		}

		foreach ($this->getElements() as $name => $element) {
			// Set a label if possible and not already set.
			if (array_key_exists($name, $metadata['title'])) {
				if (!$element->getLabel()) {
					$element->setLabel($metadata['title'][$name]);
				}
				if ($element instanceof Celsus_Form_Element_NullSelect) {
					$element->setDefaultTitle('--' . $metadata['title'][$name] . '--');
				}
			}

			// Set a title if possible and not already set.
			if (!$element->getAttrib('title') && array_key_exists($name, $metadata['description'])) {
				$element->setAttrib('title', $metadata['description'][$name]);
			}

		}
	}

	/**
	 * Retrieves the metadata for the service associated with this form, if any.
	 *
	 * @return array
	 */
	protected function _getServiceMetadata() {
		if (null === $this->_serviceMetadata) {
			if (!$this->hasService()) {
				return false;
			}
			$service = $this->_service;
			return $service::getFieldMetadata();
		}
		return $this->_serviceMetadata;
	}

	/**
	 * Returns the fields in this form that reference another model.
	 *
	 * @return array
	 */
	protected function _getReferencedFields($includeParentReferences = false) {
		$return = array();
		$referenceTypes = array(
			Celsus_Model_Service::FIELD_TYPE_REFERENCE
		);
		if ($includeParentReferences) {
			$referenceTypes[] = Celsus_Model_Service::FIELD_TYPE_PARENT_REFERENCE;
		}
		$metadata = $this->_getServiceMetadata();
		if ($metadata) {
			foreach ($metadata['type'] as $name => $type) {
				if (in_array($type, $referenceTypes)) {
					$return[$name] = $metadata['reference'][$name];
				}
			}
		}
		return $return;
	}

	/**
	 * Sets up multi-options for this form.
	 */
	public function setMultiOptions() {
		$references = $this->_getReferencedFields();

		foreach ($references as $name => $serviceClass) {
			$element = $this->getElement($name);
			$data = $serviceClass::getLookupValues();
			$this->getElement($name)->setMultiOptions($data);
		}

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
	 * Filter a name to only allow valid variable characters.  Overrides
	 * parent and allows hyphens.
	 *
	 * @param  string $value
	 * @param  bool $allowBrackets
	 * @return string
	 */
	public function filterName($value, $allowBrackets = false) {
		$charset = '^a-zA-Z0-9_\x7f-\xff-';
		if ($allowBrackets) {
			$charset .= '\[\]';
		}
		return preg_replace('/[' . $charset . ']/', '', (string) $value);
	}
}

?>