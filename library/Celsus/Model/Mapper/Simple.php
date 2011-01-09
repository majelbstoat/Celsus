<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Model
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

/**
 * Wraps underlying results in the correct model and caches method calls.
 *
 * @category Celsus
 * @package Celsus_Model
 */
class Celsus_Model_Mapper_Simple extends Celsus_Model_Mapper {

	/**
	 * The underlying object.
	 *
	 * @var Celsus_Model_Base_Interface
	 */
	protected $_base = null;

	/**
	 * The class of the base.
	 *
	 * @var string
	 */
	protected $_baseClass = null;

	public function __construct($service, Celsus_Model_Base_Interface $base = null) {
		$this->_service = $service;

		if (null === $base) {
			$this->_baseClass = str_replace('Model_Service', 'Model_Base', $this->_service);
		} else {
			$this->_base = $base;
		}
	}

	/**
	 * Sets the base object to use for retrieval.
	 *
	 * @return Celsus_Model_Mapper_Simple
	 */
	public function setBase($base = null) {

		if (is_null($base)) {
			$base = $this->_baseClass;
		}

		if (is_string($base)) {
			$config = array('service' => $this->_service);
			$base = new $base($config);
		}

		if (!$base instanceof Celsus_Model_Base_Interface) {
			throw new Celsus_Exception("Model base must implement Celsus_Model_Base_Interface");
		}

		$this->_base = $base;
		return $this;
	}

	/**
	 * Enables lazy loading of the underlying object.
	 *
	 * @return Celsus_Model_Base_Interface
	 */
	public function getBase() {
		if (null === $this->_base) {
			$this->setBase();
		}
		return $this->_base;
	}

	/**
	 * Performs the requested function from the underlying, optionally caching the result and wrapping
	 * it in a model or model set.
	 *
	 * @param string $method
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments) {
		$base = $this->getBase();

		if ($this->_caching) {
			$key = md5($this->_service . $method . serialize($arguments));
			$data = Celsus_Cache::load($key);
			if (false === $data) {
				$data = call_user_func_array(array($base, $method), $arguments);
				Celsus_Cache::save($data, $key);
			}
			$this->_caching = false;
		} else {
			// Not caching, just pass through.
			$data = call_user_func_array(array($base, $method), $arguments);
		}

		if ($this->_single || $this->_multiple) {
			if ($data->toArray()) {
				// Now, wrap the response in the correct model.
				$config = array(
					'data' => $data,
					'service' => $this->_service
				);

				$replacement = $this->_single ? 'Model' : 'Model_Set';
				$modelClass = str_replace('Model_Service', $replacement, $this->_service);

				$return = new $modelClass($config);
			} else {
				$return = null;
			}
		} else {
			// No wrapping for this function, just return the raw result.
			$return = $data;
		}

		// Reset wrappers.
		$this->_single = $this->_multiple = false;
		return $return;
	}

}
