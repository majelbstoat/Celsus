<?php

class Celsus_Controller_Action_Helper_Processor  extends Zend_Controller_Action_Helper_Abstract {

	protected $_processor = null;

	protected function _getControllerProcessor() {
		if (null === $this->_processor) {
			$context = Zend_Controller_Action_HelperBroker::getStaticHelper('ContextSwitch')->getCurrentContext();
			if (!$context) {
				$context = 'form';
			}
			$class = 'Celsus_Controller_Processor_' . ucfirst($context);
			$this->_processor = new $class($this->getActionController());
		}
		return $this->_processor;
	}

	public function direct() {
		return $this;
	}

	public function __call($method, $arguments) {

		if (!method_exists($this->_getControllerProcessor(), $method)) {
			throw new Celsus_Exception("Unknown method");
		}

		return call_user_func_array(array($this->_getControllerProcessor(), $method), $arguments);
	}

}
