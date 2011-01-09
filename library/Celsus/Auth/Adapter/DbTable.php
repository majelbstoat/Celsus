<?php

class Celsus_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable {

	/**
	 * Name of table to join to used for complex authentications.
	 *
	 * @var string
	 */
	protected $_joinName;

	/**
	 * Condition of join used for complex authentications.
	 *
	 * @var string
	 */
	protected $_joinCond;

	public function setJoin($name, $cond) {
		$this->_joinName = $name;
		$this->_joinCond = $cond;
		return $this;
	}

	protected function _authenticateCreateSelect() {
		$dbSelect = parent::_authenticateCreateSelect();
		if ($this->_joinName) {
			$dbSelect->join($this->_joinName, $this->_joinCond);
		}
		return $dbSelect;
	}
}

?>