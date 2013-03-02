<?php

class Celsus_Mixer_Component_Group extends Celsus_Data_Collection implements Celsus_Mixer_Source_Interface {

	protected $_objectClass = 'Celsus_Mixer_Component';

	protected $_type = null;

	public static function getTypes() {}

	public static function getSource($type, array $config = array()) {}

	public function extractLabelsToArray() {
		$return = array();
		foreach ($this->_objects as $component) {
			$return[] = $component->label;
		}
		return $return;
	}

	public function extractConfidencesToArray() {
		$return = array();
		foreach ($this->_objects as $component) {
			$return[$component->label] = $component->confidence;
		}
		return $return;
	}

	public function setType($type) {
		$this->_type = $type;

		return $this;
	}

	public function getType() {
		return $this->_type;
	}

	public function noteOperation($operation) {
		foreach ($this->_objects as $component) {
			array_unshift($component->operations, $operation);
		}

		return $this;
	}

	/**
	 * Allowing a component group to yield itself allows the result of one mixing to be used
	 * as the input to another.
	 *
	 * @see Celsus_Mixer_Source_Interface::yield()
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
