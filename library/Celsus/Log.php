<?php

class Celsus_Log extends Zend_Log {

	const LOGGER_DEFAULT = 'default';

	public static $_loggers = array();

	protected $_defaultTimestampFormat = 'Y-m-d H:i:s';

	/**
	 * Factory to construct the logger and one or more writers
	 * based on the configuration array
	 *
	 * @param  array|Zend_Config Array or instance of Zend_Config
	 * @return Zend_Log
	 * @throws Zend_Log_Exception
	 */
	public static function getLogger($loggerName)
	{
		if (!array_key_exists($loggerName, self::$_loggers)) {
			$config = Zend_Registry::get('config')->log->$loggerName;

			if ($config instanceof Zend_Config) {
				$config = $config->toArray();
			}

			if (!is_array($config) || empty($config)) {
				/** @see Zend_Log_Exception */
				require_once 'Zend/Log/Exception.php';
				throw new Zend_Log_Exception('Configuration must be an array or instance of Zend_Config');
			}

			$log = new static;

			if (array_key_exists('timestampFormat', $config)) {
				if (null != $config['timestampFormat'] && '' != $config['timestampFormat']) {
					$log->setTimestampFormat($config['timestampFormat']);
				}
				unset($config['timestampFormat']);
			}

			$log->addWriter($config);

			self::$_loggers[$loggerName] = $log;
		}

		return self::$_loggers[$loggerName];
	}

	public static function error($message, $label = '', $loggerName = self::LOGGER_DEFAULT) {
		return self::logWithLogger(self::ERR, $message, $label, $loggerName);
	}

	public static function info($message, $label = '', $loggerName = self::LOGGER_DEFAULT) {
		return self::logWithLogger(self::INFO, $message, $label, $loggerName);
	}

	public static function warn($message, $label = '', $loggerName = self::LOGGER_DEFAULT) {
		return self::logWithLogger(self::WARN, $message, $label, $loggerName);
	}

	protected static function logWithLogger($priority, $message, $label, $loggerName) {
		$logger = self::getLogger($loggerName);
		$logger->log($message, $priority, array('label' => $label));
	}

}