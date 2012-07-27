<?php

class Celsus_Response_Model {

	const RESPONSE_TYPE_DEFAULT = 'default';
	const RESPONSE_TYPE_REDIRECT = 'redirect';
	const RESPONSE_TYPE_DELETED = 'deleted';

	/**
	 * The response type set by the controller
	 *
	 * @var string
	 */
	protected $_responseType = self::RESPONSE_TYPE_DEFAULT;

	/**
	 * The data to use for this response.
	 *
	 * @var array
	 */
	protected $_data = array();


	/**
	 * Gets the data from this response model.
	 *
	 * @return array
	 */
	public function getData() {
		return $this->_data;
	}

	/**
	 * @param array
	 * @return Celsus_Response_Model
	 */
	public function setData(array $data) {
		$this->_data = $data;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getResponseType() {
		return $this->_responseType;
	}

	/**
	 * @param string
	 * @return Celsus_Response_Model
	 */
	public function setResponseType($responseType) {
		$this->_responseType = $responseType;
		return $this;
	}

	/**
	 * Allows direct setting of a value on the object.
	 *
	 * @param string $field
	 * @param mixed $value
	 */
	public function __set($field, $value) {
		$this->_data[$field] = $value;
		return $this;
	}

	/**
	 * Allows direct safe access to fields on the object.
	 *
	 * Returns null if
	 * @param string $field
	 */
	public function __get($field) {
		return isset($this->_data[$field]) ? $this->_data[$field] : null;
	}

}