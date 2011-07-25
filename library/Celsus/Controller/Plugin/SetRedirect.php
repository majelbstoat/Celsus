<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

/**
 * Sets a redirect session so that users can come back to the same page once they log in.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Plugin_SetRedirect extends Zend_Controller_Plugin_Abstract {

	/* (non-PHPdoc)
	 * @see library/Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
	 * @todo Only set and trigger the session if there is no login session.
	 */
	public function dispatchLoopShutdown() {

		if ('auth' != Zend_Controller_Front::getInstance()->getRequest()->getControllerName()) {
			$redirectSession = new Zend_Session_Namespace('Redirect');
			$redirectSession->location = $_SERVER['REQUEST_URI'];
		}
	}
}