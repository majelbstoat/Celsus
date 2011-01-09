<?php

class Celsus_Form_Decorator_ClientSideValidated extends Zend_Form_Decorator_Abstract {

	protected $_validatedFields = array();

	/**
	 * Plugin loader to load validation objects for form-level validation.
	 *
	 * @var Zend_Loader_PluginLoader
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
			$targets = array();
			if (isset($validator['situations'])) {
				foreach ($validator['situations'] as $name => $validators) {
					$targets[$name] = true;
					$situations[$name] = $this->_generateClientSideValidation($name, $this->_convertFormValidation($validators), false);
				}
			}
			foreach ($validator['conditions'] as $name => $validators) {
				$targets[$name] = true;
				$clientsideValidation = $this->_generateClientSideValidation($name, $this->_convertFormValidation($validators));
				if (isset($validator['message'])) {
					foreach ($clientsideValidation as $rule => & $message) {
						$message = $validator['message'];
					}
				}
				$conditions[$name] = $clientsideValidation;
			}
			foreach (array_keys($targets) as $target) {
				$this->_validatedFields[$target][] = array(
					'situations' => $situations,
					'conditions' => $conditions
				);
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
			$r = new ReflectionClass($name);
			if ($r->hasMethod('__construct')) {
				return $r->newInstanceArgs((array) $options);
			} else {
				return new $name;
			}
		}
	}

	/**
	 * Converts an array of validator strings and optional arguments into a validator object suitable
	 * for creating client side validation code from.
	 *
	 * @var array $validators
	 * @return array
	 */
	protected function _convertFormValidation($validators) {
		foreach ($validators as $validator) {
			if (is_array($validator)) {
				// This is a validator with options
				$return[$validator[0]] = $this->_createValidator($validator[0], $validator[1]);
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
				if ($ignoreBlank && ('mandatory' != $test) && ('Empty' != $base)) {
					// For non-mandatory constraints, we don't want to validate if the value is empty.
					$test = "'' === \$V('$name') || " . $test;
				}
				$tests[$test] = $message;
			}
		}
		return $tests;
	}

	protected function _buildValidators() {
		if (!$this->_validatedFields) {
			return '';
		}

		ob_start();
		$formName = $this->getElement()->getName();
		echo "Celsus.addLoadEvent(Celsus.Validation.initialise, '$formName', [";
		$output = array();
		foreach ($this->_validatedFields as $element => $validationSet) {
			$object = new StdClass();
			$object->field = $element;
			foreach ($validationSet as $validation) {
				if (isset($validation['situations']) && $validation['situations']) {
					$situationRules = array();
					foreach ($validation['situations'] as $field => $situations) {
						foreach (array_keys($situations) as $situation) {
							$situationRules[] = ('mandatory' == $situation) ? "\$V('$field', false)" : $situation;
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
							$info->condition = "\$V('$field', false)";
							$info->mandatory = true;
						} else {
							$info->condition = str_replace('%element%', $field, $constraint);
						}
						$info->message = $message;
						$info->target = $field;
						$object->constraints[] = $info;
					}
				}
			}
			$output[] = Zend_Json::encode($object);
		}
		echo implode(',', $output);
		$validateNow = Zend_Controller_Front::getInstance()->getRequest()->isPost() ? 'true' : 'false';
		echo "], '$formName', $validateNow);";
		return ob_get_clean();
	}

	public function render($content) {
		$elements = $this->getElement()->getElements();
		foreach ($elements as $element) {
			$validators = $element->getValidators();
			$required = $element->isRequired();
			if ($required || $validators) {
				$this->_processElement($element, $validators);
			}
		}
		$this->_processForm();

		$validation = $this->_buildValidators();
		return $content . '<script type="text/javascript">' . $validation . '</script>';
	}
}

?>