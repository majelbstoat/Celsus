<?php

abstract class Celsus_Mixer_Operation implements Celsus_Mixer_Operation_Interface {

	protected $_config = array();

	protected $_defaultConfig = array();

	public function __construct(array $config = array()) {
		$this->_config = array_merge($this->_defaultConfig, $config);
	}

	public function process(Celsus_Mixer_Component_Group $results) {
		$return = $this->_process($results);

		$return->noteOperation($this->_name);

		return $return;
	}

	/**
	 * Separates a set of components into an array of arrays, keyed by the specified field.
	 *
	 * @param array $components
	 * @param string $field
	 */
	protected function _separateByField(Celsus_Mixer_Source_Interface $components, $field, $takeAll = true) {

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
	 * @param Celsus_Mixer_Component_Group $results
	 * @return Celsus_Mixer_Component_Group
	 */
	abstract protected function _process(Celsus_Mixer_Component_Group $results);
}