<?php
class Celsus_Controller_Response_Http extends Zend_Controller_Response_Http {

	/**
	 * Send all headers
	 *
	 * Sends any headers specified. Sends the HTTP response code first, along with its
	 * textual name.
	 *
	 * @return Zend_Controller_Response_Abstract
	 */
	public function sendHeaders()
	{
		// Only check if we can send headers if we have headers to send
		if (count($this->_headersRaw) || count($this->_headers) || (200 != $this->_httpResponseCode)) {
			$this->canSendHeaders(true);
		} elseif (200 == $this->_httpResponseCode) {
			// Haven't changed the response code, and we have no headers
			return $this;
		}

		$httpResponseCodeText = Celsus_Http::getName($this->_httpResponseCode);
		header("HTTP/1.1 $this->_httpResponseCode $httpResponseCodeText");

		foreach ($this->_headersRaw as $header) {
			header($header);
		}

		foreach ($this->_headers as $header) {
			header($header['name'] . ': ' . $header['value'], $header['replace']);
		}

		return $this;
	}
}
