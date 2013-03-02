<?php

/**
 * Allows for selection of items by an integer field value.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_Select_ByConfidence extends Celsus_Mixer_Operation_Select_ByInteger {

	protected $_name = 'selectByConfidence';

	protected function _prepareValues(Celsus_Mixer_Component_Group $results) {
		return $results->extractConfidencesToArray();
	}

}