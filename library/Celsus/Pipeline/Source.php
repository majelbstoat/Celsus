<?php

abstract class Celsus_Pipeline_Source implements Celsus_Pipeline_Source_Interface {

	protected static $_types = array();

	protected $_type = null;

	protected $_config = array();

	protected $_defaultConfig = array();

	public function __construct(array $config = array()) {
		$this->_config = array_merge($this->_defaultConfig, $config);
	}

	public function configure(array $config = array()) {
		$this->_config = array_merge($this->_config, $config);
	}

	public static function getTypes() {
		return static::$_types;
	}

	public function getType() {
		return $this->_type;
	}
}