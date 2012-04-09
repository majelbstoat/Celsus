<?php

class Celsus_Db_Document_Redis_Query {

	protected $_name = null;

	protected $_parameters = array();

	public function __construct($options = array()) {
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	public function setParameters($parameters) {
		foreach ($this->_parameters as $key => $value) {
			if (!isset(self::$_validParameters[$key])) {
				throw new Celsus_Exception("$key is not a valid parameter for a redis query.");
			}
		}
		$this->_parameters = $parameters;
		return $this;
	}

	public function getParameters() {
		return $this->_parameters;
	}
}

?>