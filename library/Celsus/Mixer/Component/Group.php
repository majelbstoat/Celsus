<?php

class Celsus_Mixer_Component_Group extends Celsus_Data_Collection implements Celsus_Mixer_Source_Interface {

	protected $_objectClass = 'Celsus_Mixer_Component';

	public static function getTypes() {
		return array(
			'partial'
		);
	}

	public static function getSource($type) {}

	public function yield($maximum) {
		return $this->_objects;
	}
}
