<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: TaskExecutor.php 49 2010-07-18 23:23:29Z jamie $
 */

/**
 * Provides a method for controllers to execute CLI tasks.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Action_Helper_TaskExecutor extends Zend_Controller_Action_Helper_Abstract {

	/**
	 * Determines whether the suppress the call to exit(0) when we successfully
	 * complete a task.
	 *
	 * @var bool
	 */
	protected $_suppressExit = false;

	/**
	 * The task object that will perform the work.
	 *
	 * @var Celsus_Cli_Task
	 */
	protected $_task = null;

	/**
	 * Prefix for the task class.
	 *
	 * @var string
	 */
	protected $_taskPrefix = '';

	public function execute(Celsus_Cli_Getopt $getopt, $taskPrefix = '') {
		if (!isset($getopt->task)) {
			throw new Celsus_Cli_Exception("Task must be specified!");
		}
		$this->_taskPrefix = $taskPrefix;

		// Get the normalised task name, and see if it exists.
		$taskClass = $this->_taskPrefix . $this->_normaliseTaskName($getopt->task);
		if (!class_exists($taskClass, true)) {
			throw new Celsus_Cli_Exception("Invalid task '$getopt->task' specified.");
		}

		// Create a new task object and execute it.
		$this->_task = new $taskClass($this->getActionController());
		$this->_task->execute();

		// Exit, unless we explicitly don't want to (e.g., for testing).
		if (!$this->_suppressExit) {
			exit(0);
		}
	}

	protected function _normaliseTaskName($name) {
		$filtered = str_replace('_', ' ', (string) $name);
		$filtered = ucwords(strtolower($filtered));
		$filtered = str_replace(' ', '', $filtered);
		return $filtered;
	}

	public function suppressExit($suppressExit) {
		if (!is_bool($suppressExit)) {
			throw new Celsus_Cli_Exception(__FUNCTION__ . " must be called with a boolean value");
		}
		$this->_suppressExit = $suppressExit;
	}

}