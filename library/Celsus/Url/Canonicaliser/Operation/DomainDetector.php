<?php

/**
 * Looks at the host of the URL and determines whether we can do any
 * domain-specific canonicalisation.
 *
 * @author majelbstoat
 */
class Celsus_Url_Canonicaliser_Operation_DomainDetector extends Celsus_Url_Canonicaliser_Operation {

	protected $_name = 'domainDetector';

	protected $_domains = array(
		'/^(.*)\.youtube\.[a-z]+$/' => 'Youtube'
	);

	protected function _process(Celsus_Pipeline_Result_Interface $results) {
		$url = $results;

		foreach ($this->_domains as $pattern => $handler) {
			if (preg_match($pattern, $url->host)) {
				$class = str_replace('DomainDetector', 'Domain_' . $handler, get_class($this));
				$operation = new $class();
				$this->_pipeline->addOperation($operation);
				break;
			}
		}

		return $url;
	}
}