<?php

class Celsus_View_Model {

	/**
	 * The renderer used to render this model.
	 *
	 * @var Celsus_View_Renderer
	 */
	protected $_renderer = null;

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

	/**
	 * The parent view model, if one exists.
	 *
	 * @var Celsus_View_Model
	 */
	protected $_parent = null;

	public function __construct(array $data = null) {
		if (null !== $data) {
			$this->_data = $data;
		}
	}

	/**
	 * @return Celsus_View_Model
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * @param Celsus_View_Model $parent
	 * @return Celsus_View_Model
	 */
	public function setParent(Celsus_View_Model $parent) {
		$this->_parent = $parent;
		return $this;
	}

	/**
	 * @return Celsus_View_Renderer
	 */
	public function getRenderer() {
		return $this->_renderer;
	}

	/**
	 * @param Celsus_View_Renderer
	 * @return Celsus_View_Model
	 */
	public function setRenderer(Celsus_View_Renderer $renderer) {
		$this->_renderer = $renderer;
		return $this;
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
		$child->setParent($this);
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

	/**
	 * Renders this model and returns it as a string.
	 *
	 * Simply requests that the defined renderer render this model.
	 *
	 * @return string
	 */
	public function render() {
		return $this->_renderer->render($this);
	}
}