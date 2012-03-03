<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Record.php 72 2010-09-14 01:56:33Z jamie $
 */

/**
 * Record layout functionality.
 *
 * @defgroup Celsus_Record Celsus Record
 */

/**
 * Defines default record functionality, for displaying a single record on a page.
 *
 * @ingroup Celsus_Record
 */
abstract class Celsus_Record extends Celsus_Form {

	/**
	 * The URL where this record can be edited.
	 *
	 * @var string
	 */
	protected $_editUrl = null;

	/**
	 * Creates a new record object.
	 *
	 * @param array $options
	 */
	final public function __construct($options = array()) {
		Zend_Form::__construct($options);

		$this->addElementPrefixPath('Celsus_Record_Decorator', 'Celsus/Record/Decorator', 'decorator');
		$this->addPrefixPath('Celsus_Record_Decorator', 'Celsus/Record/Decorator', 'decorator');

	  $this->addAttribs(array('class' => 'record standard-form'));

		// Setup the elements for this form.
		$this->_setupElements($options);

		$this->setDecorators(array(
	    'FormElements',
			'Record'
		));

		// Adds default filters and decorators to the form elements.
		$this->addElementDecorators(array('Output'));

		// Add elements to a default display group if there isn't already one.
		if (!$this->getDisplayGroup('main')) {
			$this->addDisplayGroup($this->getElementNames(), 'main', array(
				'order' => 0,
				'legend' => $this->_getMainLegend(),
			));
		}

		$this->setDisplayGroupDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			'FieldSet',
		));
	}

	public function getElementNames() {
		return array_keys($this->_elements);
	}

	protected function _getMainLegend() {
		$service = $this->getService();
		return $service::getTitle() . ' Details';
	}

	/**
	 * Adds element decorators as specified
	 *
	 * @param  array $decorators
	 * @return Celsus_Record
	 */
	public function addElementDecorators(array $decorators) {

		// Add service-specific decorators.
		$this->_addCustomDecorators();

		// Now, ensure that referenced fields display the name of the record, not the id.
		foreach ($this->_getReferencedFields(true) as $field => $reference) {
			$this->_elements[$field]->addDecorator('LookupReference', array('service' => $reference));
		}

		// Finally, add the standard output decorator to provide labels and formatting.
		foreach ($this->getElements() as $element) {
			$element->addDecorators($decorators);
		}
		return $this;
	}

	public function getDescription() {
		$service = $this->_service;
		return $service::getDescription($this->getValues());
	}

	public function getValues($suppressArrayNotation = false) {
		$return = array();
		foreach ($this->getElements() as $name => $element) {
			$return[$name] = $element->getValue();
		}
		return $return;
	}

	/**
	 * Adds model-specific custom decorators.
	 */
	protected function _addCustomDecorators() {}

	public function populate(array $values) {
		Zend_Form::populate($values);

		// Now populate from references.
		$references = $this->_getReferencedFields();
		foreach (array_keys($references) as $field) {
			if (is_array($values[$field])) {
				$this->getElement($field)->setValue($values[$field]['id']);
			}
		}

		// Finally, set an edit link for the record.
		$service = $this->_service;
		$this->_editUrl = Celsus_Application::tenantUrl() . '/' . $service::getPrimaryLocation() . '/' . $values['id'] . '/edit/';
	}

	public function getEditUrl() {
		return $this->_editUrl;
	}

	/**
	 * Adds elements for output to the record.
	 *
	 * @param array $options
	 */
	protected function _setupElements(array $options = null) {
		$service = $this->_service;
		$fields = $service::getFields();
		foreach ($fields as $field => $definition) {
			$element = $this->addElement(new Celsus_Form_Element_Display($field))->getElement($field);
			$element->setLabel($definition['title']);
		}
	}

	/**
	 * Syntactic sugar to allow direct echoing of the record, proxies to render.
	 */
	public function __toString() {
		try {
			$return = $this->render();
			return $return;
		} catch (Exception $e) {
			return $e->getMessage();
			trigger_error($e->getMessage(), E_USER_WARNING);
			return '';
		}
	}
}

?>