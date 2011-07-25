<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Routing.php 50 2010-07-19 01:40:10Z jamie $
 */

/**
 * class_description
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Plugin_Routing extends Zend_Controller_Plugin_Abstract {

	/**
	 * Determines what the default module should be, based on the server name.
	 * If we are visiting a subsite, checks that the subsite is valid.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return bool
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$frontController = Zend_Controller_Front::getInstance();

		// Default error handling from the web module.
		$errorHandler = $frontController->getPlugin('Zend_Controller_Plugin_ErrorHandler');
		$errorHandler->setErrorHandlerModule('web');

		$server = $_SERVER['SERVER_NAME'];

		// Check that the organisational and reseller accounts exist and match.
		$subdomains = explode('.', $server);
		$organisation->name = $subdomains[0];
		$resellerDomain = implode('.', array_slice($subdomains, 1));
		Zend_Registry::set('organisation', $organisation);

		$organisationModel = new Henka_Model_Organisation();
		$organisation = $organisationModel->fetchRow($organisationModel->select()->where('name = ?', $organisation->name));
		if (!$organisation) {
			throw new Zend_Controller_Dispatcher_Exception("Invalid organisation specified: $organisation->name");
		}

		// Now determine the reseller, so we can skin the application.
		$resellerModel = new Henka_Model_Reseller();
		$reseller = $resellerModel->fetchRow($resellerModel->select()->where('id = ?', $organisation->reseller));

		if ($resellerDomain != $reseller->domain) {
			// The specified organisation has somehow arrived at the wrong reseller
			// parent domain, so send them to the correct URL.
			// @todo - actually forward them to the correct URL.
			forward();
		}

		// Store organisational and reseller information for later.
		Zend_Registry::set('organisation', $organisation);
		Zend_Registry::set('reseller', $reseller);
	}
}

?>