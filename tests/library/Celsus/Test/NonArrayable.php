<?php

class Celsus_Test_NonArrayable {

	protected $_var = "value";

	public function __construct() {}

	public function getVar() {
		return $this->_var;
	}
}