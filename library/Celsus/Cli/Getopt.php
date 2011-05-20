<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Cli
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Getopt.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Command line parsing class that doesn't throw exceptions when the options listed don't match.
 *
 * @category Celsus
 * @package Celsus_Cli
 */
class Celsus_Cli_Getopt extends Zend_Console_Getopt {

	protected static $_baseRules = array(
		'environment|e-w' => 'Sets the application environment',
		'task|t=w' => 'The task to perform',
	);

	public function __construct($argv = null, $getoptConfig = array()) {
		parent::__construct(self::$_baseRules, $argv, $getoptConfig);
	}

	/**
	 * Parse command-line arguments for a single option.
	 * Ignores options that aren't listed in a rule, rather than
	 * throwing an Exception.
	 *
	 * @param  string $flag
	 * @param  mixed  $argv
	 * @throws Zend_Console_Getopt_Exception
	 * @return void
	 */
	protected function _parseSingleOption($flag, &$argv) {
		if ($this->_getoptConfig[self::CONFIG_IGNORECASE]) {
			$flag = strtolower($flag);
		}
		if (!isset($this->_ruleMap[$flag])) {
			return;
		}
		$realFlag = $this->_ruleMap[$flag];
		switch ($this->_rules[$realFlag]['param']) {
			case 'required':
				if (count($argv) > 0) {
					$param = array_shift($argv);
					$this->_checkParameterType($realFlag, $param);
				} else {
					require_once 'Zend/Console/Getopt/Exception.php';
					throw new Zend_Console_Getopt_Exception("Option \"$flag\" requires a parameter.", $this->getUsageMessage());
				}
				break;
			case 'optional':
				if (count($argv) > 0 && substr($argv[0], 0, 1) != '-') {
					$param = array_shift($argv);
					$this->_checkParameterType($realFlag, $param);
				} else {
					$param = true;
				}
				break;
			default:
				$param = true;
		}
		$this->_options[$realFlag] = $param;
	}

}
