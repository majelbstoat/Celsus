<?php

/**
 * Acts as a pipeline through which an URL can be canonicalised.
 *
 * Provides a convenient static method to canonicalise multiple URLs.
 *
 * @author majelbstoat
 */
class Celsus_Url_Canonicaliser extends Celsus_Pipeline {

	protected $_operators = array();

	/**
	 * Canonicalises one or many URLs.
	 *
	 * @param string|array $urls
	 */
	public static function canonicalise($urls) {
		if (!is_array($urls)) {
			$urls = array($urls);
		}

		$canonicalisedUrls = new Celsus_Url_Canonicalised_Group($urls);

		$canonicaliser = new self();

		foreach ($canonicalisedUrls as $canonicalisedUrl) {
			$canonicalisedUrl = $canonicaliser
				->setInputData($canonicalisedUrl)
				->process();
		}

		return $canonicalisedUrls;

	}

	protected function querySources($sources) {
		throw new Celsus_Exception("The Url Canonicaliser doesn't use sources", Celsus_Http::INTERNAL_SERVER_ERROR);
	}

	protected function _getDefaultOperations() {
		return array(
			new Celsus_Url_Canonicaliser_Operation_DomainDetector(),
		);
	}

}