<?php

abstract class Celsus_TestCase extends PHPUnit_Framework_TestCase {


	public function assertObjectIsInstanceOf($object, $interfaceOrObject) {
		$this->_incrementAssertionCount();

		if (!($object instanceof $interfaceOrObject)) {
			$requiredClassName = is_string($interfaceOrObject) ? $interfaceOrObject : get_class($object);
			$className = get_class($object);
			$this->fail("Failed asserting that object of type $className is an instance of $requiredClassName");
		}
	}

	/**
	 * Increment assertion count
	 *
	 * @return void
	 */
	protected function _incrementAssertionCount() {
		$stack = debug_backtrace();
		foreach (debug_backtrace() as $step) {
			if (isset($step['object'])
				&& $step['object'] instanceof PHPUnit_Framework_TestCase
			) {
				if (version_compare(PHPUnit_Runner_Version::id(), '3.3.0', 'lt')) {
					break;
				} elseif (version_compare(PHPUnit_Runner_Version::id(), '3.3.3', 'lt')) {
					$step['object']->incrementAssertionCounter();
				} else {
					$step['object']->addToAssertionCount(1);
				}
				break;
			}
		}
	}

}