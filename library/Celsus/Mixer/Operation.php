<?php

abstract class Celsus_Mixer_Operation implements Celsus_Mixer_Operation_Interface {

	/**
	 * Separates a set of components into an array of arrays, keyed by the specified field.
	 *
	 * @param array $components
	 * @param string $field
	 */
	protected function _separateByField($components, $field) {

		$return = array();

		foreach ($components as $component) {
			$key = $component->$field;

			if (empty($return[$key])) {
				$return[$key] = array();
			}

			$return[$key][] = $component;
		}

		return $return;
	}
}