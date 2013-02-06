<?php

abstract class Celsus_Mixer_Source implements Celsus_Mixer_Source_Interface {

	protected static $_types = array();

	public static function getTypes() {
		return static::$_types;
	}
}