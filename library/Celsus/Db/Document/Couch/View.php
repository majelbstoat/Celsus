<?php

class Celsus_Db_Document_Couch_View {

	protected $_designDocument = null;

	protected $_name = null;

	protected $_parameters = array();

	protected $_post = null;

	protected static $_validParameters = array(
		'key' => '',
		'startkey' => '',
		'endkey' => '',
		'limit' => '',
		'stale' => '',
		'descending' => '',
		'skip' => '',
		'group' => '',
		'group_level' => '',
		'reduce' => '',
		'include_docs' => '',
		'inclusive_end' => ''
	);

	public function __construct($options = array()) {
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	public function setDesignDocument($designDocument) {
		$this->_designDocument = $designDocument;
		return $this;
	}

	public function getDesignDocument() {
		return $this->_designDocument;
	}

	public function setName($name) {
		$this->_name = $name;
		return $this;
	}

	public function getName() {
		return $this->_name;
	}

	public function setPost($post) {
		$this->_post = $post;
		return $this;
	}

	public function setParameters($parameters) {
		foreach ($this->_parameters as $key => $value) {
			if (!isset(self::$_validParameters[$key])) {
				throw new Celsus_Exception("$key is not a valid parameter for a view.");
			}
		}
		$this->_parameters = $parameters;
		return $this;
	}

	public function getParameters() {
		return $this->_parameters;
	}
}

?>