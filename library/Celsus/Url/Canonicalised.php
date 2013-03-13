<?php

class Celsus_Url_Canonicalised extends Celsus_Pipeline_Result {

	/**
	 * The url scheme.
	 *
	 * @var string $scheme
	 */
	public $scheme = null;

	/**
	 * The url host.
	 *
	 * @var string $host
	 */
	public $host = null;

	/**
	 * The url host.
	 *
	 * @var string $path
	 */
	public $path = null;

	/**
	 * The exploded url query components after the ?.
	 *
	 * @var string $query
	 */
	public $queryComponents = array();

	/**
	 * The url fragment after the #.
	 *
	 * @var string $fragment
	 */
	public $fragment = null;

	/**
	 * The original, uncanonicalised form.
	 *
	 * @var string $original
	 */
	public $original = null;

	public function canonicalised() {
		$combined = $this->scheme . '://' . $this->host . $this->path;

		if ($this->queryComponents) {
			$combined .= "?" . http_build_query($this->queryComponents);
		}

		if ($this->fragment) {
			$combined .= "#$this->fragment";
		}

		return $combined;
	}

	protected function _importData($data) {
		$components = parse_url($data);

		if (false === $components) {
			throw new Celsus_Exception("Cannot canonicalise invalid URL $data", Celsus_Http::INTERNAL_SERVER_ERROR);
		}

		$this->scheme = $components['scheme'];
		$this->host = $components['host'];
		$this->path = $components['path'];

		if (isset($components['query'])) {
			parse_str($components['query'], $this->queryComponents);
		}

		if (isset($components['fragment'])) {
			$this->fragment = $components['fragment'];
		}

		$this->original = $data;
	}

	/**
	 * outputs the canonicalised form of the URL.
	 *
	 * @var string $url
	 */
	public function __toString() {
		return $this->canonicalised();
	}
}