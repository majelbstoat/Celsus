<?php

/**
 * Class providing output for a single record in the database.
 * For flexibility, a record is really just
 * a form, but with Display Attributes.
 */
abstract class Celsus_Record extends Zend_Form {

	/**
	 * Whether or not lookup decorators should cache their database column.
	 *
	 * @var boolean
	 */
	protected $_cacheLookups = false;

	/**
	 * The model representing this data.
	 *
	 * @var Celsus_Model
	 */
	protected $_model = null;

	/**
	 * Creates a new record object.
	 *
	 * @param array $options
	 */
	final public function __construct($options = null) {
		parent::__construct($options);

		$this->addElementPrefixPath('Celsus_Record_Decorator', 'Celsus/Record/Decorator', 'decorator');
		$this->addPrefixPath('Celsus_Record_Decorator', 'Celsus/Record/Decorator', 'decorator');
		$this->addElementPrefixPath('BAM_Record_Decorator', 'BAM/Record/Decorator', 'decorator');
		$this->addPrefixPath('BAM_Record_Decorator', 'BAM/Record/Decorator', 'decorator');
		$this->addElementPrefixPath('Celsus_Form_Decorator', 'Celsus/Form/Decorator', 'decorator');
		$this->addPrefixPath('Celsus_Form_Decorator', 'Celsus/Form/Decorator', 'decorator');

		if (is_array($options)) {
			$this->setOptions($options);
		}

		$this->setDecorators(array(
	    'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			'Record'
		));

	  $this->addAttribs(array('class' => 'record'));

		// Setup the elements for this form.
		$this->_setupElements($options);

		// Adds default filters and decorators to the form elements.
		$this->addElementDecorators(array('Output'));

		// Add defalt labels to those with none set.
		$this->_addDefaultLabels();
	}

	/**
	 * Adds element decorators as specified
	 *
	 * @param  array $decorators
	 * @return Celsus_Record
	 */
	public function addElementDecorators(array $decorators) {

		// First add lookup decorators to referenced fields.
		$references = $this->_model->getMapper()->getLookupReferences();

		foreach ($references as $field => $reference) {
			$this->getElement($field)->addDecorator('LookupReference', array(
				'referenced' => $reference,
				'cacheLookup' => $this->_cacheLookups
			));
		}

		// Now, add model-specific decorators.
		$this->_addCustomDecorators();

		// Finally, add the standard output decorator to provide labels and formatting.
		foreach ($this->getElements() as $element) {
			$element->addDecorators($decorators);
		}
		return $this;
	}

	public function getRecordTitle() {
		$descriptiveField = $this->_model->getDescriptiveField();
		return $this->getElement($descriptiveField)->getUnfilteredValue();
	}

	/**
	 * Adds model-specific custom decorators.
	 */
	protected function _addCustomDecorators() {}

	/**
	 * Set record state from options array
	 *
	 * @param array $options
	 * @return Celsus_Record
	 */
	public function setOptions(array $options) {
		if (isset($options['model'])) {
			$this->_model = $options['model'];
			unset($options['model']);
		}

		if (isset($options['cacheLookups'])) {
			$this->_cacheLookups = true;
			unset($options['cacheLookups']);
		}
	}

	/**
	 * Adds elements for output to the record.
	 *
	 * @param array $options
	 */
	protected function _setupElements(array $options) {
		if (null == $this->_model) {
			return false;
		}

		$fields = $this->_model->getDefaultFields();

		foreach ($fields as $field) {
			$this->addElement(new Celsus_Form_Element_Display($field));
		}
	}

	/**
	 * Adds default labels to those that have one set for this model (if one is registered) and don't already have a label.
	 *
	 * @return boolean
	 */
	protected function _addDefaultLabels() {
		if (null == $this->_model) {
			return false;
		}

		$titles = $this->_model->getColumnTitles();

		foreach ($this->getElements() as $name => $element) {
			if (!$element->getLabel()) {
				if (array_key_exists($name, $titles)) {
					$element->setLabel($titles[$name]);
				} else {
					$element->setLabel($name);
				}
			}
		}
	}

	/**
	 * Populates the data and renders the record.
	 *
	 * @param Zend_View_Interface $view
	 * @return string
	 */
	public function render(Zend_View_Interface $view = null) {
		return parent::render($view);
	}

	/**
	 * Syntactic sugar to allow direct echoing of the record, proxies to render.
	 */
	public function __toString() {
		try {
			$return = $this->render();
			return $return;
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
			return '';
		}
	}
}

?>