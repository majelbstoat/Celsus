<?php

class Celsus_OpenGraph_Object {

	protected $_properties = array(
		'og' => array(
			'type' => null,
			'url' => null,
			'title' => null,
			'description' => null,
			'image' => null
		),
		'fb' => array(
			'app_id' => null
		)
	);

	public function setProperty($prefix, $property, $value) {
	}

	public function getPrefixes() {
		return array_keys($this->_properties);
	}

}