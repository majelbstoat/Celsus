<?php

class Celsus_Set_Operation_Union extends Celsus_Set_Operation_Collection {

	/**
	 * Magic method that determines if at least one of the elements of a set
	 * is to be included, according to the elements' criteria.
	 *
	 * @param string $method
	 * @param array $arguments
	 * @return boolean
	 */
	public function __call($method, $arguments) {
		// First check that the method we are calling is defined in the specified interface.
		if (!method_exists($this->_setInterface, $method)) {
			throw new Celsus_Exception("$method is not defined in $this->_setInterface");
		}
		// Iterate through the elements, calling the supplied method name on each.
		// If any return true, this union is true.
		foreach ($this->_elements as $element) {
			if (call_user_func_array(array($element, $method), $arguments)) {
				return true;
			}
		}
		return false;
	}
}

?>