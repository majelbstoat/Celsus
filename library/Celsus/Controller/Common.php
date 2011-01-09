<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Common.php 53 2010-08-10 02:53:53Z jamie $
 */

/**
 * Default application controller providing standard CRUD functionality based on convention.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
abstract class Celsus_Controller_Common extends Zend_Rest_Controller implements Zend_Acl_Resource_Interface {

	/**
	 * The resource Id of this controller.
	 *
	 * @var string
	 */
	protected $_resourceId = null;

	public function getResourceId() {
		return $this->_resourceId;
	}

	public function __call($method, $arguments) {
		if ('Action' == substr($method, -6)) {
			$controller = $this->getRequest()->getControllerName();
			return $this->_redirect("/$controller");
		}
		throw new Celsus_Exception("Invalid method call", Celsus_Error::EXCEPTION_APPLICATION_ERROR);
	}
}
