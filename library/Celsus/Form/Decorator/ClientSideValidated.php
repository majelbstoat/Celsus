<?php

class Celsus_Form_Decorator_ClientSideValidated extends Zend_Form_Decorator_Abstract {

	protected $_validatedFields = array();

	protected $_validationTriggers = array();

	/**
	 * Plugin loader to load validation objects for form-level validation.
	 *
	 * @var Zend_Loader_PluginLoader $_validationLoader
	 */
	protected $_validationLoader = null;


	/**
	 * Converts server side validating classes for an element into client side
	 * validation code.
	 *
	 * @param Zend_Form_Element $element
	 * @param array $validators
	 */
	protected function _processElement($element, $validators) {

		$name = $element->getName();
		$constraints = $this->_generateClientSideValidation($name, $validators);

		if (!isset($constraints['mandatory']) && $element->isRequired()) {
			$constraints['mandatory'] = "This field is required.";
		}

		if ($constraints) {
			$this->_validatedFields[$name][] = array(
				'situations' => array(),
				'conditions' => array($name => $constraints)
			);
			if (!array_key_exists($name, $this->_validationTriggers)) {
				$this->_validationTriggers[$name] = array();
			}
			$this->_validationTriggers[$name][$name] = true;
		}

	}

	/**
	 * Converts form-level validation into client side validation code.
	 */
	protected function _processForm() {
		$formValidators = $this->getElement()->getValidators();

		$constraints = array();
		foreach ($formValidators as $validator) {
			$situations = array();
			$conditions = array();
			if (isset($validator['situations'])) {
				foreach ($validator['situations'] as $name => $validators) {
					$situations[$name] = $this->_generateClientSideValidation($name, $this->_convertFormValidation($validators), false);
				}
			}
			foreach ($validator['conditions'] as $name => $validators) {
				// Make the field trigger itself.
				if (!array_key_exists($name, $this->_validationTriggers)) {
					$this->_validationTriggers[$name] = array();
				}
				$this->_validationTriggers[$name][$name] = true;

				$clientsideValidation = $this->_generateClientSideValidation($name, $this->_convertFormValidation($validators));
				if (isset($validator['message'])) {
					foreach ($clientsideValidation as $rule => & $message) {
						$message = $validator['message'];
					}
				}
				$this->_validatedFields[$name][] = array(
					'situations' => $situations,
					'conditions' => array($name => $clientsideValidation)
				);

				// Now ensure that each element that determines whether the condition needs to be satisfied trigger the checks on this element.
				foreach (array_keys($situations) as $trigger) {
					$this->_validationTriggers[$trigger][$name] = true;
				}
			}
		}
	}

	/**
	 * Creates a validator object from basenames, for use in form-level validation.
	 *
	 * @param string $base
	 * @param array $options
	 * @return Zend_Validate_Abstract
	 */
	protected function _createValidator($base, $options = array()) {
		if (null == $this->_validationLoader) {
			$this->_validationLoader = new Zend_Loader_PluginLoader(
				array('Zend_Validate_' => 'Zend/Validate/')
			);
			foreach ($this->getElement()->getValidatorNamespaces() as $namespace) {
				$prefix = $namespace . '_';
				$this->_validationLoader->addPrefixPath($prefix, str_replace('_', '/', $prefix));
			}
		}

		$name = $this->_validationLoader->load($base);
		if (empty($options)) {
			return new $name;
		} else {
			//return new $name($options);
			$r = new ReflectionClass($name);
			if ($r->hasMethod('__construct')) {
				return $r->newInstanceArgs($options);
			} else {
				return new $name;
			}
		}
	}

	/**
	 * Converts an array of validator strings and optional arguments into a validator object suitable
	 * for creating client side validation code from.
	 *
	 * @var array|string $validators
	 * @return array
	 */
	protected function _convertFormValidation($validators) {
		if (!is_array($validators)){
			$validators = array($validators);
		}
		foreach ($validators as $validator) {
			if (is_array($validator)) {
				// This is a validator with options
				$arguments = $validator;
				$validator = array_shift($arguments);
				$return[$validator] = $this->_createValidator($validator, $arguments);
			} else {
				// This is just a plain class base name.
				$return[$validator] = $this->_createValidator($validator);
			}
		}
		return $return;
	}

	/**
	 * Given the element name and set of validators, returns client-side validation snippets
	 *
	 * @param $name The element name.
	 * @param $validators An array of validator objects attached to this element.
	 * @param $ignoreBlank If set, adds a clause that ignores the validation if the field is empty.
	 */
	protected function _generateClientSideValidation($name, $validators, $ignoreBlank = true) {
		$tests = array();
		foreach ($validators as $type => $validator) {
			$components = explode('_', $type);
			$base = (false !== strpos($type, '_')) ? end($components) : $type;
			if ($validation = $validator->getClientSideValidation($name)) {
				list($test, $message) = $validation;
				//if ($ignoreBlank && ('mandatory' != $test) && ('Empty' != $base)) {
					// For non-mandatory constraints, we don't want to validate if the value is empty.
				//	$test = "_inputs.$name.blank() || " . $test;
			//	}
				$tests[$test] = $message;
			}
		}
		return $tests;
	}

	protected function _buildValidators() {
		if (!$this->_validatedFields) {
			return '';
		}

		$output = new stdClass();
		foreach ($this->_validatedFields as $element => $validationSet) {
			$object = new StdClass();

			if (array_key_exists($element, $this->_validationTriggers)) {
				// Mark this element as triggering others.
				$object->triggers = array_keys($this->_validationTriggers[$element]);
			}

			foreach ($validationSet as $validation) {
				if (isset($validation['situations']) && $validation['situations']) {
					$situationRules = array();
					foreach ($validation['situations'] as $field => $situations) {
						foreach (array_keys($situations) as $situation) {
							$situationRules[] = ('mandatory' == $situation) ? "!_inputs[model].$field.blank()" : str_replace('%element%', "_inputs[model].$field", $situation);
						}
					}
					$situation = implode(' && ', $situationRules);
				} else {
					$situation = true;
				}
				foreach ($validation['conditions'] as $field => $condition) {
					foreach ($condition as $constraint => $message) {
						$info = new StdClass();
						$info->situation = $situation;
						if ($constraint == 'mandatory') {
							$info->condition = "!_inputs[model].$field.blank()";
							$info->mandatory = true;
						} else {
							$info->condition = str_replace('%element%', "_inputs[model].$field", $constraint);
						}
						$info->message = $message;
						$info->target = $field;
						$object->constraints[] = $info;
					}
				}
			}
			$output->$element = $object;
		}

		$formName = $this->getElement()->getInternalName();
		$constraints = Zend_Json::encode($output);

		return <<<FORM_VALIDATION
(function($) {
	Celsus.Validation.Rules["$formName"] = $constraints;
})(jQuery);
FORM_VALIDATION;
	}

	public function render($content) {
		$this->_processForm();
		$form = $this->getElement();
		foreach ($form->getElements() as $element) {
			$validators = $element->getValidators();
			$required = $element->isRequired();
			if ($required || $validators) {
				$this->_processElement($element, $validators);
			}
		}

		// Write all this information to a file in js/validation/model-{serialised-validation-rules}.js

		$validation = $this->_buildValidators();
		file_put_contents(PROJECT_ROOT . '/html/js/validation/' . $form->getInternalName() . '.js', $validation);

		// Set up the jQuery view helpers to render this form.
		$jQuery = Zend_Layout::getMvcInstance()->getView()->getHelper('jQuery');
		$jQuery->addPlugin('validate');
//			->addPlugin('formerize')
//			->addValidation($form->getInternalName());

		//foreach ($form->getAdditionalValidation() as $name => $validation) {
			//$jQuery->addValidation($validation, $name);
		//}

		$jQuery->addPlugin($form->getName(), 'validation');

		if (Zend_Controller_Front::getInstance()->getRequest()->isPost()) {
			$jQuery->addJavascript("Celsus.Validation.validateOnLoad = true;");
		}


		return $content;
	}
}

?>
