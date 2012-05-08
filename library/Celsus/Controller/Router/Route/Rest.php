<?php

public function match($path) {
	Celsus_Log::info("Path is $path");
}

public function assemble($data = array(), $reset = false, $encode = false) {
	Celsus_Log::info($data, "Assembly Data");
}

public static function getInstance(Zend_Config $config) {
	$instance = new self($routes);

	return $instance;
}

