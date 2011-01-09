<?php

abstract class Celsus_Set_Operation_Abstract {


	/**
	 * The classname of the set to be operated on.
	 *
	 * @var string
	 */
	protected $_setInterface = null;

	/**
	 * Creates a new set operator whose elements all implement the specified
	 * interface.
	 *
	 * @param string $setInterface
	 */
	public function __construct($setInterface) {
		if (!is_string($setInterface)) {
			throw new Celsus_Exception("Interface must be a string.");
		}
		if (!interface_exists($setInterface, true)) {
			throw new Celsus_Exception("$setInterface is not a valid interface.");
		}
		$this->_setInterface = $setInterface;
	}
}
?>