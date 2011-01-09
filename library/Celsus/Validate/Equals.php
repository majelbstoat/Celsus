
<?php

class Celsus_Validate_Equals extends Zend_Validate_Abstract
{
	const NOT_EQUAL = 'notEqual';

	protected $_match = null;

	protected $_messageVariables = array(
		'match' => '_match',
	);

	protected $_messageTemplates = array(
		self::NOT_EQUAL => "'%value%' does not equal %match%"
	);

	public function __construct($match) {
		$this->setMatch($match);
	}
	
	public function setMatch($match) {
		$this->_match = $match;
	}
	
	public function getMatch() {
		return $this->_match;
	}
	
	public function isValid($value) {

		if (null == $this->_match) {
			throw new Celsus_Exception("Nothing to test for equality against");
		}

		$this->_setValue($value);
		
		if ($value != $this->_match) {
			$this->_error(self::NOT_EQUAL);
			return false;
		}
		return true;
	}
	
	/**
	 * Returns code snippet used for client-side validation.
	 * 
	 * @param string $name The name of the element.
	 * @return array The constraint and message.
	 */
	public function getClientSideValidation($name) {
		$match = $this->getMatch();
		$test = "\$F('$name') == $match";
		$message = "This must equal $match";
		return array($test, $message);
	}	
}

