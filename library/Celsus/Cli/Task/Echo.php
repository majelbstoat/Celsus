<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Cli
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

/**
 * Simple task that echoes the supplied string back on the command line.
 *
 * @category Celsus
 * @package Celsus_Cli
 */
class Celsus_Cli_Task_Echo extends Celsus_Cli_Task {

	/**
	 * Rules for the echo task.
	 *
	 * @var array
	 */
	protected $_getoptRules = array(
		'string|s=s' => 'The string to echo'
	);


	/**
	 * The echo task must have a string to echo.
	 *
	 * @return bool
	 */
	protected function _verifyGetoptRules() {
		if (null == $this->_parameters['string']) {
			$this->_messages['string'] = 'You must specify a string to echo.';
			return false;
		}
		return true;
	}

	protected function _execute() {
		echo "Hello, " . $this->_parameters['string'] . PHP_EOL;
	}
}
