<?php

class Celsus_Environment {

	protected static $_isBigEndian = null;

	public function isBigEndian() {
		if (null === self::$_isBigEndian) {
			$test = unpack('L', pack('V', 1));
			self::$_isBigEndian = ($test[1] !== 1);
		}
		return self::$_isBigEndian;
	}
}