<?php

class Celsus_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable implements Celsus_Auth_Adapter_Interface {

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
	protected $_joinCondition;

	public function setJoin($name, $condition) {
		$this->_joinName = $name;
		$this->_joinCondition = $condition;
		return $this;
	}

	protected function _authenticateCreateSelect() {
		$dbSelect = parent::_authenticateCreateSelect();
		if ($this->_joinName) {
			$dbSelect->join($this->_joinName, $this->_joinCondition);
		}
		return $dbSelect;
	}
}

?>