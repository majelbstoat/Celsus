<?php

/**
 * Extends Zend_Validate_Regex to work with named Regexes and provide client side validation.
 */
class Celsus_Validate_Regex extends Zend_Validate_Regex {
	
	protected static $_regexes = array(
		'MicrosoftGuid' => array(
			'regex' => '/^[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}$/i',
			'message' => 'Please enter a valid Microsoft GUID'
		),
		'NoWhitespace' => array(
			'regex' => '/^\S+$/i',
			'message' => 'Please enter a value without spaces'
		),		
		'Subnet' => array(
			'regex' => '/^(([0-9]|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.){3}([0-9]|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\/([1-9]|[1|2]\d|3[0-2])$|(0\.0\.0\.0\/0)$/',
			'message' => 'Please enter a valid subnet'
		),		
	);
	
	/**
	 * The type of this regex.
	 * 
	 * @var string
	 */
	protected $_type;
	
	/**
	 * Turns the named regex type into a Zend_Validate_Regex constructed using the stored pattern.
	 * 
	 * @var param $type
	 */
	public function __construct($type) {
		if (!isset(self::$_regexes[$type])) {
			throw new Celsus_Exception("$type is not a valid regex type");
		}
		parent::__construct(self::$_regexes[$type]['regex']);
		$this->setMessage(self::$_regexes[$type]['message'], parent::NOT_MATCH);
		$this->_type = $type;
	}
	
	/**
	 * Returns code snippet used for client-side validation.
	 * 
	 * @param string $name The name of the element.
	 * @return array The constraint and message.
	 */
	public function getClientSideValidation($name) {
		$test = self::$_regexes[$this->_type]['regex'] . ".test(\$F('$name'))";
		$message = self::$_regexes[$this->_type]['message'];
		return array($test, $message);
	}
}

?>