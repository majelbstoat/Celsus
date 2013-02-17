<?php

class Celsus_Set_Operation_Difference extends Celsus_Set_Operation {

	/**
	 * The elements to be included in this set.
	 *
	 * @var array
	 */
	protected $_includes = array();

	/**
	 * The elements to be excluded in this set.
	 *
	 * @var array
	 */
	protected $_excludes = array();

	/**
	 * Magic method that determines if all of the includes of a set
	 * are included, and all the excludes of a set are excluded,
	 * according to the elements' criteria.
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

		if (!$this->_includes && !$this->_excludes) {
			// We don't have any elements.
			return false;
		}

		// Iterate through the includes, calling the supplied method name on each.
		// If any return false, this difference is false.
		foreach ($this->_includes as $include) {
			if (!call_user_func_array(array($include, $method), $arguments)) {
				return false;
			}
		}

		// Now, iterate through the excludes, calling the supplied method name on each.
		// If any return true, this difference is false.
		foreach ($this->_excludes as $exclude) {
			if (call_user_func_array(array($exclude, $method), $arguments)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Adds an element to the exclusion set.
	 *
	 * @param StdClass $element
	 */
	public function addExclude($element) {
		if ($element instanceof $this->_setInterface || $element instanceof Celsus_Set_Operation_Abstract) {
			$this->_excludes[] = $element;
		} else {
			throw new Celsus_Exception("Element must implement $this->_setInterface or Set");
		}
	}

	/**
	 * Adds an array of elements to the exclusion set all in one go.
	 *
	 * @param array $elements
	 */
	public function addExcludes(array $elements) {
		foreach ($elements as $element) {
			$this->addExclude($element);
		}
	}

	/**
	 * Adds an element to the inclusion set.
	 *
	 * @param StdClass $element
	 */
	public function addInclude($element) {
		if ($element instanceof $this->_setInterface || $element instanceof Celsus_Set_Operation_Abstract) {
			$this->_includes[] = $element;
		} else {
			throw new Celsus_Exception("Element must implement $this->_setInterface or Set");
		}
	}

	/**
	 * Adds an array of elements to the inclusion set all in one go.
	 *
	 * @param array $elements
	 */
	public function addIncludes(array $elements) {
		foreach ($elements as $element) {
			$this->addInclude($element);
		}
	}
}
