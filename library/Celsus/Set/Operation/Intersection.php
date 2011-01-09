<?php

/**
 * Returns the intersection of members (where a case is true for all members).
 */
class Celsus_Set_Operation_Intersection extends Celsus_Set_Operation_Collection {

	/**
	 * Magic method that determines if all of the elements of a set
	 * are included, according to the elements' criteria.
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

		if (!$this->_elements) {
			// We don't have any elements.
			return false;
		}

		// Iterate through the elements, calling the supplied method name on each.
		// If any return false, this intersection is false.
		foreach ($this->_elements as $element) {
			if (!call_user_func_array(array($element, $method), $arguments)) {
				return false;
			}
		}
		return true;
	}
}

?>