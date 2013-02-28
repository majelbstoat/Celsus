<?php

class Celsus_Data_Collection extends Celsus_Data implements Iterator, Countable, ArrayAccess {

	/**
	 * Counting is used as a proxy for truthiness of collections, so maintain an internal
	 * count of objects to speed the calling of it.
	 *
	 * @var int $_count
	 */
	protected $_count = 0;

	protected $_objectClass = 'Celsus_Data_Object';

	protected $_objects = array();

	public function __construct($items = array()) {
		$classname = get_class($this);
		foreach ($items as $item) {
			if (get_class($item) !== $classname) {
				$item = new $this->_objectClass($item);
			}
			$this->_objects[] = $item;
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

	public function slice($length, $offset = 0) {
		return new static(array_slice($this->_objects, $offset, $length));
	}

	public function filter($callback) {
		$return = new static;
		foreach ($this->_objects as $object) {
			if (true === call_user_func($callback, $object)) {
				$return[] = $object;
			}
		}
		return $return;
	}

	public function append(self $collection) {
		foreach ($collection as $object) {
			$this->_objects[] = $object;
			$this->_count++;
		}

		return $this;
	}

	/**
	 * Sorts the objects in the collection using the specified compare function.
	 *
	 * By default, the sort is unstable because it relies on PHP's underlying
	 * usort(), but it can be made stable by supplying a decorate and undecorate
	 * function and performing a Schwartzian Transfer.  This can also be used to
	 * obviate the need for an expensive comparison function which is
	 * repetitively called.
	 *
	 * This method re-keys the internal object array.
	 *
	 * @param callable $compare
	 * @param callable $decorate
	 * @param callable $undecorate
	 */
	public function sort($compare, $decorate = null, $undecorate = null) {

		$items = (null !== $decorate) ? call_user_func($decorate, $this->_objects) : $items;

		usort($items, $compare);

		$this->_objects = (null !== $undecorate) ? call_user_func($undecorate, $items) : $items;

		return $this;
	}

	public function offsetSet($offset, $value) {
		if (null === $offset) {
			// Allow simple array-like additions.
			$this->_objects[] = $value;
			$this->_count++;
		} else {
			// Directly specified offsets also work.
			if (!isset($this->_objects[$offset])) {
				$this->_count++;
			}
			$this->_objects[$offset] = $value;
		}
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

	public function toArray() {
		$return = array();
		foreach ($this->_objects as $object) {
			$return[] = $object->toArray();
		}
		return $return;
	}

	public function __call($method, $args) {

		// First, determine if we are trying to render.
		if ('to' == substr($method, 0, 2)) {
			$format = substr($method, 2);
			return $this->_output($format);
		}

		// Otherwise, execute the required method on all the contained objects.
		foreach ($this->_objects as $object) {
			call_user_func_array(array($object, $method), $args);
		}
	}
}