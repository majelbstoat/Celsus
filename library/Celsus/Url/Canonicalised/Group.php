<?php

class Celsus_Url_Canonicalised_Group extends Celsus_Pipeline_Result_Group {

	protected $_objectClass = 'Celsus_Url_Canonicalised';

	public function extractCanonicalisedFormByOriginalFormToArray() {
		$return = array();
		foreach ($this->_objects as $canonicalisedUrl) {
			$return[$canonicalisedUrl->original] = $canonicalisedUrl->canonicalised();
		}
		return $return;
	}
}