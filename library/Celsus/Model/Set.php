<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Model
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Set.php 60 2010-08-21 07:48:27Z jamie $
 */

/**
 * Representation of a set of models that can be manipulated as a group
 *
 * @category Celsus
 * @package Celsus_Model
 */
abstract class Celsus_Model_Set extends Celsus_Data_Collection {

	/**
	 * Mapper that relates the business model to the underlying.
	 *
	 * @var Celsus_Model_Mapper
	 */
	public $_mapper = null;

	/**
	 * Constructs a new Celsus Model.
	 *
	 * @param array $options
	 */
	public function __construct(array $config) {

		// Check that a service is specified.
		if (!isset($config['mapper'])) {
			throw new Celsus_Exception("Can't instantiate a model set without a service definition");
		}
		$this->_mapper = $config['mapper'];

		// Check that data was specified.
		if (!isset($config['data'])) {
			throw new Celsus_Exception("Can't instantiate a model instance without data");
		}

		foreach ($config['data'] as $data) {
			$this->_objects[] = new $this->_objectClass(array(
				'mapper' => $config['mapper'],
				'data' => $data
			));
		}
	}
}