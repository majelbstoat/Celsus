<?php

class Celsus_Auth_Storage_Cache implements Zend_Auth_Storage_Interface {

	const KEY_PREFIX = '__auth-';

	/**
	 * The key to store against.
	 *
	 * @string.
	 */
	protected $_key;

	protected $_identityField = 'username';

	public function __construct($identityField) {
		$this->_identityField = $identityField;
	}

	public static function setIdentityField($identityField) {
		self::$_identityField = $identityField;
	}

	public function clear() {
		Celsus_Cache::delete($this->_key);
	}

	public function isEmpty() {
		return !!Celsus_Cache::load($this->_key);
	}

	public function read() {
		return Celsus_Cache::load($this->_key);
	}

	public function write($contents) {
		$value = $contents[$this->$_identityField];
		$this->_key = md5(self::KEY_PREFIX . $value);
		Celsus_Cache::save($contents, $this->_key);
	}

	public function getKey() {
		return $this->_key;
	}

}
