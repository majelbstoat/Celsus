<?php

/**
 * Data transfer object which allows for tight control of the fields that can be set.
 *
 * Basically a glorified array with some restrictions.
 *
 * @author majelbstoat
 */
class Celsus_Data_Struct extends Celsus_Data {

	public function setFromArray($data) {
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}

	public function __set($key, $value) {
		// Non-public fields may not be set.
	}

	public function __get($key) {
		// Non-public fields may not be retrieved.
		return null;
	}
}