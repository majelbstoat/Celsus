<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: CliTestCase.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Mock CLI request class for testing CLI applications.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Request_CliTestCase extends Celsus_Controller_Request_Cli {

	/**
	 * Raw POST body
	 *
	 * @var string|null
	 */
	protected $_rawBody;

	/**
	 * Set raw POST body
	 *
	 * @param  string $content
	 * @return Zend_Controller_Request_HttpTestCase
	 */
	public function setRawBody($content) {
		$this->_rawBody = (string) $content;
		return $this;
	}

	/**
	 * Get RAW POST body
	 *
	 * @return string|null
	 */
	public function getRawBody() {
		return $this->_rawBody;
	}

	/**
	 * Clear raw POST body
	 *
	 * @return Zend_Controller_Request_HttpTestCase
	 */
	public function clearRawBody() {
		$this->_rawBody = null;
		return $this;
	}

}
