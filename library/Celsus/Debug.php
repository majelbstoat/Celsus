<?php

class Celsus_Debug {

	public static function print_r($data) {
		echo "<xmp>" . print_r($data, true) . "</xmp>";
	}
}