<?php
/**
 * ChromePHP Copyright 2012 Craig Campbell https://github.com/ccampbell/chromephp
 *
 * Modified by Jamie Talbot.
 */

/**
 * Server Side Chrome PHP debugger class
 *
 * @package ChromePhp
 * @author Craig Campbell <iamcraigcampbell@gmail.com>
 */
class Celsus_Log_Writer_Mock extends Zend_Log_Writer_Abstract implements Countable {

	protected $_logData = array();

	/**
	 * Create a new instance of Zend_Log_Writer_Syslog
	 *
	 * @param  array|Zend_Config $config
	 * @return Zend_Log_Writer_Syslog
	 */
	public static function factory($config) {
		return new static();
	}

	public function count() {
		return count($this->_logData);
	}

	protected function _write($event) {
		$this->_logData[] = array(
			'label' => $event['label'],
			'value' => $event['message'],
			'type' =>  $event['priority']
		);
	}

}