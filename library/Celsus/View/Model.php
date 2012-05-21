<?php

class Celsus_View_Model {

	/**
	 * A set of child view models to be rendered as part of this model.
	 *
	 * @var Celsus_View_Model[] $_children
	 */
	protected $_children = array();

	/**
	 * The data in this view model.
	 *
	 * @var array $_data
	 */
	protected $_data = array();

	public function __construct(array $data = null) {
		if (null !== $data) {
			$this->_data = $data;
		}
	}

	public function getData() {
		return $this->_data;
	}

	public function setData(array $data) {
		foreach ($data as $key => $value) {
			$this->_data[$key] = $value;
		}
	}

	public function setChild($name, Celsus_View_Model $child) {
		$this->_children[$name] = $child;
		return $this;
	}

	public function getChildren() {
		return $this->_children;
	}

	public function hasChildren() {
		return !!$this->_children;
	}

	/**
	 * @return Celsus_View_Model
	 */
	public function setChildren(array $children) {
		foreach ($children as $name => $child) {
			$this->setChild($name, $child);
		}
		return $this;
	}
}