<?php

abstract class Celsus_Pipeline_Result_Group extends Celsus_Data_Collection implements Celsus_Pipeline_Source_Interface, Celsus_Pipeline_Result_Interface {

	protected $_objectClass = 'Celsus_Pipeline_Result';

	protected $_type = null;

	public static function getTypes() {}

	public static function getSource($type, array $config = array()) {}

	public function setType($type) {
		$this->_type = $type;

		return $this;
	}

	public function getType() {
		return $this->_type;
	}

	public function noteOperation($operation) {
		foreach ($this->_objects as $object) {
			$object->noteOperation($operation);
		}

		return $this;
	}

	/**
	 * Allowing a result group to yield itself allows the result of one pipeline to be used
	 * as the input to another.
	 *
	 * @see Celsus_Pipeline_Source_Interface::yield()
	 */
	public function yield(array $config = array()) {
		return $this;
	}

}