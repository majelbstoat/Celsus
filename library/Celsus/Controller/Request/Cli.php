<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Cli.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Defines a CLI request where arguments come from the command line.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Request_Cli extends Zend_Controller_Request_Abstract {

	protected $_getopt = null;

	protected $_argv = null;

	public function setArguments($argv) {
		if (!is_array($argv)) {
			throw new Celsus_Cli_Exception("Arguments must be an array");
		}
		$this->_argv = $argv;
	}

	/**
	 * Gets the Getopt associated with this request.
	 *
	 * @return Celsus_Cli_Getopt
	 */
	public function getGetopt() {
		if (null == $this->_getopt) {
			$this->_getopt = new Celsus_Cli_Getopt($this->_argv);
			$this->_getopt->parse();
		}
		return $this->_getopt;
	}

	public function setPathInfo($pathInfo = null) {}

}

?>