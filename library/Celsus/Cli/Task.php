<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Cli
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Task.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Represents a single command line task, with argument verification.
 *
 * @category Celsus
 * @package Celsus_Cli
 */
abstract class Celsus_Cli_Task {

	/**
	 * Error messages associated with this task.
	 */
	protected $_messages = array();

	/**
	 * The parameters used for this task.
	 *
	 * @var array
	 */
	protected $_parameters = array();

	/**
	 * Specific rules for interop
	 * @var array
	 */
	protected $_getoptRules = array();

	public function __construct(Celsus_Controller_Cli $controller) {
		$getopt = $controller->getRequest()->getGetopt();

		if ($this->_getoptRules) {
			// Add task-specific rules and re-parse the command line.
			$getopt->addRules($this->_getoptRules);
			$getopt->parse();
			foreach ($this->getGetoptRuleNames() as $var) {
				// Store command-line parameters for execution.
				if (isset($getopt->$var)) {
					$this->_parameters[$var] = $getopt->$var;
				}
			}
		}
	}

	/**
	 * Checks that supplied arguments were valid, calls the _execute() function,
	 * and exits.
	 *
	 * @throws Celsus_Cli_Exception When bad arguments are specified.
	 */
	public function execute() {
		if (!$this->_verifyGetoptRules()) {
			throw new Celsus_Cli_Exception(implode(PHP_EOL, $this->_messages));
		}
		$this->_execute();
	}

	/**
	 * Actually performs the task.
	 *
	 * @return void
	 */
	abstract protected function _execute();

	/**
	 * Checks that a valid set of parameters was supplied.
	 *
	 * @return bool
	 */
	abstract protected function _verifyGetoptRules();

	/**
	 * Gets the getopt rules for this task.
	 *
	 * @return array
	 */
	public function getGetoptRules() {
		return $this->_getoptRules;
	}

	/**
	 * Gets the names associated with the getopt rules.
	 *
	 * @return array
	 */
	public function getGetoptRuleNames() {
		$ruleNames = array();
		foreach (array_keys($this->_getoptRules) as $rule) {
			list($ruleNames[]) = explode('|', $rule);
		}
		return $ruleNames;
	}

}