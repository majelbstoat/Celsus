<?php

class Celsus_Data_Collection implements Iterator, Countable, ArrayAccess {

	/**
	 * Counting is used as a proxy for truthiness of collections, so maintain an internal
	 * count of objects to speed the calling of it.
	 *
	 * @var int $_count
	 */
	protected $_count = 0;

	protected $_objectClass = 'Celsus_Data_Object';

	protected $_objects = array();

	public function __construct($objects) {
		foreach ($objects as $object) {
			$this->_objects[] = new $this->_objectClass($object);
			$this->_count++;
		}
	}

	public function first() {
		return ($this->_objects) ? $this->_objects[0] : null;
	}

	public function count() {
		return $this->_count;
	}

	public function current() {
		return current($this->_objects);
	}

	public function key() {
		return key($this->_objects);
	}

	public function next() {
		return next($this->_objects);
	}

	public function rewind() {
		return reset($this->_objects);
	}

	public function valid() {
		return (false !== $this->current());
	}

	public function offsetSet($offset, $value) {
		if (!isset($this->_objects[$offset])) {
			$this->_count++;
		}
		$this->_objects[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->_objects[$offset]);
	}

	public function offsetUnset($offset) {
		if (isset($this->_objects[$offset])) {
			$this->_count--;
		}
		unset($this->_objects[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->_objects[$offset]) ? $this->_objects[$offset] : null;
	}

	public function __call($method, $args) {
		foreach ($this->_objects as $object) {
			call_user_func_array(array($object, $method), $args);
		}
	}

	public function toArray() {
		$return = array();
		foreach ($this->_objects as $object) {
			$return[] = $object->toArray();
		}
		return $return;
	}
}