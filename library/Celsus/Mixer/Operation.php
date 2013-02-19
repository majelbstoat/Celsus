<?php

abstract class Celsus_Mixer_Operation implements Celsus_Mixer_Operation_Interface {

	/**
	 * Separates a set of results into an array of arrays, keyed by the specified field.
	 *
	 * @param array $results
	 * @param string $field
	 */
	protected function _separateByField(array $results, $field) {

		$return = array();

		foreach ($results as $result) {
			$key = $result->$field;
			if (isset($return[$key])) {
				$return[$key][] = $result;
			} else {
				$return[$key] = array($result);
			}
		}

		return $return;
	}
}