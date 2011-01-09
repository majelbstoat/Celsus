<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Cli.php 49 2010-07-18 23:23:29Z jamie $
 */

/**
 * CLI controller
 *
 * @category Celsus
 * @package Celsus_Controller
 */
abstract class Celsus_Controller_Cli extends Zend_Controller_Action {

	/**
	 * Make sure we can only access these tasks from the command line.
	 */
	public function init() {
		$request = $this->getRequest();
		if (!$request instanceof Celsus_Controller_Request_Cli) {
			throw new Celsus_Exception('Command Line Tasks may only be accessed from the command line');
		}

		return parent::init();
	}
}