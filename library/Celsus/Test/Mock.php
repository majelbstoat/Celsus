<?php

abstract class Celsus_Test_Mock extends PHPUnit_Framework_TestCase {

	protected $_callStack = array();

	public function __call($method, $arguments) {
		$this->_callStack[] = array(
			'method' => $method,
			'arguments' => $arguments
		);
		return call_user_func_array(array($this, "_$method"), $arguments);
	}

	/**
	 * Resets the changes set up by the mock, in reverse order.
	 */
	public function reset() {
		foreach (array_reverse($this->_callStack) as $call) {
			call_user_func_array(array($this, "_reset" . ucfirst($call['method'])), $call['arguments']);
		}
	}
}