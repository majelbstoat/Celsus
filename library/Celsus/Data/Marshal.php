<?php

abstract class Celsus_Data_Marshal implements Celsus_Data_Marshal_Interface {

	public static function provides() {
		return static::$_marshalledClass;
	}


}
