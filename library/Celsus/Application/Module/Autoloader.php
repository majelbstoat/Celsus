<?php

class Celsus_Application_Module_Autoloader extends Zend_Application_Module_Autoloader {

	public function __construct($options) {
		parent::__construct($options);
		$this->addResourceTypes(array(
			'modelservice' => array(
				'namespace' => 'Model_Service',
				'path' => 'models/services',
			),
			'modelbase' => array(
				'namespace' => 'Model_Base',
				'path' => 'models/bases',
			),
			'record' => array(
				'namespace' => 'Record',
				'path' => 'records',
			),
		));
	}
}
