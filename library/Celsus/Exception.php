<?php

class Celsus_Exception extends Exception {
	
	public static function production_handler($exception) {
		$logger = Zend_Registry::get('logger');
		$logger->log("Unhandled exception: " . $exception->getMessage(). "\n" . $exception->getTraceAsString(), Zend_Log::ALERT);
	}
		
}

?>