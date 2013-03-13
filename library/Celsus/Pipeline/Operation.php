<?php

abstract class Celsus_Pipeline_Operation implements Celsus_Pipeline_Operation_Interface {

	protected $_config = array();

	protected $_defaultConfig = array();

	protected $_pipeline = null;

	public function __construct(array $config = array()) {
		$this->_config = array_merge($this->_defaultConfig, $config);
	}

	public function process(Celsus_Pipeline_Result_Interface $results) {
		$return = $this->_process($results);

		$return->noteOperation($this->_name);

		return $return;
	}

	public function getName() {
		return $this->_name;
	}

	public function setPipeline(Celsus_Pipeline $pipeline) {
		$this->_pipeline = $pipeline;

		return $this;
	}

	/**
	 * Separates a set of components into an array of arrays, keyed by the specified field.
	 *
	 * @param array $components
	 * @param string $field
	 */
	protected function _separateByField(Celsus_Pipeline_Source_Interface $components, $field, $takeAll = true) {

		$return = array();

		foreach ($components as $component) {
			$keys = $component->$field;

			if (!is_array($keys)) {
				$keys = array($keys);
			} elseif (!$takeAll) {
				$keys = array_slice($keys, 0, 1);
			}

			foreach ($keys as $key) {
				if (empty($return[$key])) {
					$return[$key] = array();
				}

				$return[$key][] = $component;
			}
		}

		return $return;
	}

	/**
	 * @param Celsus_Pipeline_Result_Interface $results
	 * @return Celsus_Pipeline_Result_Interface
	 */
	abstract protected function _process(Celsus_Pipeline_Result_Interface $results);
}