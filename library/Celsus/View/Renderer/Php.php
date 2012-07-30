<?php

class Celsus_View_Renderer_Php extends Celsus_View_Renderer {

	const SCRIPT_PATH = '/php';

	protected $_template = null;

	protected $_compiledTemplate = null;

	protected $_model = null;

	protected $_data = null;

	protected $_childData = array();

	protected $_absoluteTemplate = null;

	protected function _renderChildren(Celsus_View_Model $model) {

		if (!$model->hasChildren()) {
			return;
		}

		foreach ($model->getChildren() as $child => $childModel) {
			$this->_childData[$child] = $childModel->render();
		}
	}

	public function render(Celsus_View_Model $model) {

		$this->_renderChildren($model);

		// Put the model's data in current scope.
		$this->_data = $model->getData();

		// Buffer the result of the compiled template.
		ob_start();
		include 'data://text/plain,' . $this->_getCompiledTemplate(VIEW_PATH . self::SCRIPT_PATH . $this->_template);
		return ob_get_clean();
	}

	/**
	 * Given a template path, attempts to find a compiled version from the cache.
	 *
	 * @param string $path
	 */
	protected function _getCompiledTemplate($path) {

		// @todo Store the results of the compiled template in Redis or something.
		if (false) {
			$compiledTemplate = null;
		} else {

			// Ensure that the file is readable.
			if (!is_readable($path)) {
				throw new Celsus_Exception("Missing template file at $path", Celsus_Http::INTERNAL_SERVER_ERROR);
			}

			$template = file_get_contents($path);

			// Matches tags like <?* $code ? >
			if (preg_match_all('/(?<tags><\?\*.+?\?>)/', $template, $matches)) {
				$replacements = array();
				foreach ($matches['tags'] as $tag) {
					$section = $tag;
					$section[2] = '=';
					$replacements[$tag] = $this->_compile($section);
				}
				$template = str_replace(array_keys($replacements), $replacements, $template);
			}

			$compiledTemplate = urlencode($template);
		}

		return $compiledTemplate;
	}

	protected function _compile($section) {
		ob_start();
		include 'data://text/plain,' . urlencode($section);
		return ob_get_clean();
	}

	protected function _child($name) {
		return $this->_childData[$name];
	}

	public function setTemplate($template) {
		$this->_template = $template;
		return $this;
	}

	public function __get($field) {
		return isset($this->_data[$field]) ? $this->_data[$field] : null;
	}

}