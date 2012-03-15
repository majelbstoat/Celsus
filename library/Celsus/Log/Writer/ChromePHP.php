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
class Celsus_Log_Writer_ChromePHP extends Zend_Log_Writer_Abstract {

	/**
	 * @var string
	 */
	const VERSION = '3.0';

	/**
	 * @var string
	 */
	const HEADER_NAME = 'X-ChromePhp-Data';

	/**
	 * @var string
	 */
	const GROUP = 'group';

	/**
	 * @var string
	 */
	const GROUP_END = 'groupEnd';

	/**
	 * @var string
	 */
	const GROUP_COLLAPSED = 'groupCollapsed';

	/**
	 * @var int
	 */
	protected $_timestamp;

	protected $_defaultBacktraceLevel = 1;

	protected $_levelMapping = array(
		Zend_Log::NOTICE => '',
		Zend_Log::INFO => 'info',
		Zend_Log::WARN => 'warn',
		Zend_Log::ERR => 'error',
	);

	/**
	 * @var array
	 */
	protected $_json = array(
		'version' => self::VERSION,
		'columns' => array('label', 'log', 'backtrace', 'type'),
		'rows' => array()
	);

	/**
	 * @var array
	 */
	protected $_backtraces = array();

	/**
	 * @var bool
	 */
	protected $_errorTriggered = false;

	/**
	 * Prevent recursion when working with objects referring to each other
	 *
	 * @var array
	 */
	protected $_processed = array();

	/**
	 * Create a new instance of Zend_Log_Writer_Syslog
	 *
	 * @param  array|Zend_Config $config
	 * @return Zend_Log_Writer_Syslog
	 */
	public static function factory($config) {
		return new static();
	}

	/**
	 * constructor
	 */
	private function __construct() {
		$this->_timestamp = $_SERVER['REQUEST_TIME'];
		$this->_json['request_uri'] = $_SERVER['REQUEST_URI'];
	}

	protected function _write($event) {
		$arguments = array(
			'label' => $event['label'],
			'value' => $event['message'],
			'type' => $this->_levelMapping[$event['priority']]
		);
		return $this->_log($arguments);
	}

	/**
	 * sends a group log
	 *
	 * @param string value
	 */
	public static function group() {
		return self::_log(func_get_args() + array('type' => self::GROUP));
	}

	/**
	 * sends a collapsed group log
	 *
	 * @param string value
	 */
	public static function groupCollapsed() {
		return self::_log(func_get_args() + array('type' => self::GROUP_COLLAPSED));
	}

	/**
	 * ends a group log
	 *
	 * @param string value
	 */
	public static function groupEnd() {
		return self::_log(func_get_args() + array('type' => self::GROUP_END));
	}

	/**
	 * internal logging call
	 *
	 * @param string $type
	 * @return void
	 */
	protected function _log(array $args) {
		$type = $args['type'];
		unset($args['type']);

		// nothing passed in, don't do anything
		if (count($args) == 0 && $type != self::GROUP_END) {
			return;
		}

		// default to single
		$label = $args['label'];
		$value = $args['value'];

		$this->_processed = array();
		$value = $this->_convert($value);

		$backtrace = debug_backtrace(false);

		if ($label) {
			$backtraceMessage = $label;
		}

		if (isset($backtrace[1]['file']) && isset($backtrace[1]['line'])) {
			$backtraceMessage = $backtrace[1]['file'] . ' : ' . $backtrace[1]['line'] . ' : ' . $label;
		}


		$this->_addRow($value, $backtraceMessage, $type);
	}

	/**
	 * converts an object to a better format for logging
	 *
	 * @param Object
	 * @return array
	 */
	protected function _convert($object) {
		// if this isn't an object then just return it
		if (!is_object($object)) {
			return $object;
		}

		//Mark this object as processed so we don't convert it twice and it
		//Also avoid recursion when objects refer to each other
		$this->_processed[] = $object;

		$objectAsArray = array();

		// first add the class name
		$objectAsArray['___class_name'] = get_class($object);

		// loop through object vars
		$object_vars = get_object_vars($object);
		foreach ($object_vars as $key => $value) {

			// same instance as parent object
			if ($value === $object || in_array($value, $this->_processed, true)) {
				$value = 'recursion - parent object [' . get_class($value) . ']';
			}
			$objectAsArray[$key] = $this->_convert($value);
		}

		$reflection = new ReflectionClass($object);

		// loop through the properties and add those
		foreach ($reflection->getProperties() as $property) {

			// if one of these properties was already added above then ignore it
			if (array_key_exists($property->getName(), $object_vars)) {
				continue;
			}
			$type = $this->_getPropertyKey($property);

			$property->setAccessible(true);
			$value = $property->getValue($object);

			// same instance as parent object
			if ($value === $object || in_array($value, $this->_processed, true)) {
				$value = 'recursion - parent object [' . get_class($value) . ']';
			}

			$objectAsArray[$type] = $this->_convert($value);
		}
		return $objectAsArray;
	}

	/**
	 * takes a reflection property and returns a nicely formatted key of the property name
	 *
	 * @param ReflectionProperty
	 * @return string
	 */
	protected function _getPropertyKey(ReflectionProperty $property) {
		$static = $property->isStatic() ? ' static' : '';
		if ($property->isPublic()) {
			return 'public' . $static . ' ' . $property->getName();
		}

		if ($property->isProtected()) {
			return 'protected' . $static . ' ' . $property->getName();
		}

		if ($property->isPrivate()) {
			return 'private' . $static . ' ' . $property->getName();
		}
	}

	/**
	 * adds a value to the data array
	 *
	 * @var mixed
	 * @return void
	 */
	protected function _addRow($log, $backtrace, $type) {

		// if this is logged on the same line for example in a loop, set it to null to save space
		if (in_array($backtrace, $this->_backtraces)) {
			$backtrace = null;
		}

		if ($backtrace !== null) {
			$this->_backtraces[] = $backtrace;
		}

		$row = array('', $log, $backtrace, $type);

		$this->_json['rows'][] = $row;

		$this->_writeHeader($this->_json);
	}

	protected function _writeHeader($data) {
		header(self::HEADER_NAME . ': ' . $this->_encode($data));
	}

	/**
	 * encodes the data to be sent along with the request
	 *
	 * @param array $data
	 * @return string
	 */
	protected function _encode($data) {
		return base64_encode(utf8_encode(json_encode($data)));
	}

	/**
	 * gets a setting
	 *
	 * @param string key
	 * @return mixed
	 */
	public function getSetting($key)
	{
		if (!isset($this->_settings[$key])) {
			return null;
		}
		return $this->_settings[$key];
	}
}