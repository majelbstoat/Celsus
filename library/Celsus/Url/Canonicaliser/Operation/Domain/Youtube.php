<?php

/**
 * Canonicalises Youtube URLs.
 *
 * @author majelbstoat
 */
class Celsus_Url_Canonicaliser_Operation_Domain_Youtube extends Celsus_Url_Canonicaliser_Operation {

	protected $_name = 'domainYoutube';

	protected function _process(Celsus_Pipeline_Result_Interface $results) {
		$url = $results;

		$url->host = 'www.youtube.com';
		$url->queryComponents = array_intersect_key($url->queryComponents,
			array('v' => true)
		);

		return $url;
	}
}