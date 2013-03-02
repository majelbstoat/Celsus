<?php

/**
 * Allows for selection of items by an integer field value.
 *
 * @author majelbstoat
 */
abstract class Celsus_Mixer_Operation_Select_ByInteger extends Celsus_Mixer_Operation {

	protected $_defaultConfig = array(
		'minimum' => null,
		'maximum' => null
	);

	protected function _process(Celsus_Mixer_Component_Group $results) {

		if (null === $this->_config['minimum'] && null === $this->_config['maximum']) {
			throw new Celsus_Exception("Either a minimum or a maximum must be specified");
		}

		$minimum = $this->_config['minimum'];
		$maximum = $this->_config['maximum'];

		$values = $this->_prepareValues($results);

		return $results->filter(function($result) use ($minimum, $maximum, $values) {
			if (null !== $minimum && ($values[$result->label] < $minimum))
				return false;

			if (null !== $maximum && ($values[$result->label] > $maximum))
				return false;

			return true;
		});
	}

	abstract protected function _prepareValues(Celsus_Mixer_Component_Group $results);

}