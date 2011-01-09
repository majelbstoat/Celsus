<?php

abstract class Celsus_Set_Operation_Collection extends Celsus_Set_Operation_Abstract {

	/**
	 * The elements of this set.
	 *
	 * @var array
	 */
	protected $_elements = array();

	/**
	 * Adds an element to the set.
	 *
	 * @param StdClass $element
	 */
	public function addElement($element) {
		if ($element instanceof $this->_setInterface || $element instanceof Celsus_Set_Operation_Abstract) {
			$this->_elements[] = $element;
		} else {
			throw new Celsus_Exception("Element must implement $this->_setInterface or Set");
		}
	}

	/**
	 * Adds an array of elements to the set all in one go.
	 *
	 * @param array $elements
	 */
	public function addElements(array $elements) {
		foreach ($elements as $element) {
			$this->addElement($element);
		}
	}
}
?>