<?php

echo "Bootstrapping Tests...\n\n";

defined('LIBRARY_PATH') || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library/'));

echo "Library Path is " . LIBRARY_PATH . "\n\n";

defined('TEST_LIBRARY_PATH') || define('TEST_LIBRARY_PATH', realpath(dirname(__FILE__) . '/library/'));

echo "Test Library Path is " . TEST_LIBRARY_PATH . "\n\n";

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(TEST_LIBRARY_PATH, get_include_path())));

set_include_path(implode(PATH_SEPARATOR, array(LIBRARY_PATH, get_include_path())));

function __autoload($class) {
	$file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
	include_once("$file");

	if (!class_exists($class, false) && !interface_exists($class, false)) {
		throw new Exception("Class $class could not be loaded.");
	}
}