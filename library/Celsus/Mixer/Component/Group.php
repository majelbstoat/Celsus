<?php

class Celsus_Mixer_Component_Group extends Celsus_Pipeline_Result_Group {

	protected $_objectClass = 'Celsus_Mixer_Component';

	public function extractLabelsToArray() {
		$return = array();
		foreach ($this->_objects as $component) {
			$return[] = $component->label;
		}
		return $return;
	}

	public function extractConfidencesToArray() {
		return $this->_extractFieldByLabelToArray('confidence');
	}

	public function extractSourcesToArray() {
		return $this->_extractFieldByLabelToArray('sources');
	}

	protected function _extractFieldByLabelToArray($field) {
		$return = array();
		foreach ($this->_objects as $component) {
			$return[$component->label] = $component->$field;
		}
		return $return;
	}

	/**
	 * Allowing a component group to yield itself allows the result of one mixing to be used
	 * as the input to another.
	 *
	 * Provides a mechanism by which certain items can be excluded.
	 *
	 * @see Celsus_Pipeline_Source_Interface::yield()
	 */
	public function yield(array $config = array()) {

		// If we are being asked to exclude some items, figure out what the labels of those items are.
		$labels = isset($config['exclude']) ? array_flip($config['exclude']->extractLabelsToArray()) : array();

		// Return all the items which haven't been excluded.
		return $this->filter(function($component) use ($labels) {
			return !isset($labels[$component->label]);
		});
	}
}