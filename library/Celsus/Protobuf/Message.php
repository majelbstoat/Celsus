<?php

abstract class Celsus_Data_Protobuf_Message implements ArrayAccess {

	protected $_fields = array();

	public function offsetExists($offset) {
		return isset($this->_fields[$offset]);
	}

	public function offsetSet($key, $value) {
		$this->_fields[$key] = $value;
	}

	public function offsetGet($key) {
		return $this->_fields[$key];
	}

	public function offsetUnset($key) {
		unset($this->_fields[$key]);
	}

}