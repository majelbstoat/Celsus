<?php

class Celsus_View_Renderer_Php {

	const SCRIPT_PATH = '/php';

	protected $_template = null;

	protected $_modelData = null;

	protected $_absoluteTemplate = null;

	public function render(Celsus_View_Model $model) {

		$this->_absoluteTemplate = VIEW_PATH . self::SCRIPT_PATH . '/' . $this->_template;
		if (!is_readable($this->_absoluteTemplate)) {
			throw new Celsus_Exception("View script $this->_template not found");
		}

		// Put the model's data in current scope.
		$this->_modelData = $model->getData();
		extract($this->_modelData);

		// Ensure that $model doesn't pollute the variable scope.
		unset($model);

		// Buffer the response.
		ob_start();

		// Include the template file.
		include $this->_absoluteTemplate;

		// Return the result.
		return ob_get_clean();
	}

	public function setTemplate($template) {
		$this->_template = $template;
		return $this;
	}

}