<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: MultiTenanting.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Provides support for applications that are multi-tenanted by subdomain.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Plugin_MultiTenanting extends Zend_Controller_Plugin_Abstract {

	const EXCEPTION_NO_TENANT = 'EXCEPTION_NO_TENANT';
	const EXCEPTION_NO_RESELLER = 'EXCEPTION_NO_RESELLER';

	/**
	 * Defines the database config name that we are going to check tenants against.
   *
	 * @var string
	 */
	private $_databaseAdapterName = null;

	/**
	 * Class which defines the tennant model service for this application.
	 *
	 * @var string
	 */
	private $_tenantClass = null;

	/**
	 * Class which defines the reseller model service for this application.
	 *
	 * @var string
	 */
	private $_resellerClass = null;

	public function __construct($databaseAdapterName, $tenantClass, $resellerClass = null) {
		$this->_databaseAdapterName = $databaseAdapterName;

		if (!in_array('Celsus_MultiTenant_Service_Tenant_Interface', class_implements($tenantClass))) {
			throw new Celsus_Exception("Tenant Class must implement Celsus_MultiTenant_Service_Tenant_Interface");
		}
		$this->_tenantClass = $tenantClass;

		if (null !== $resellerClass) {
			if (!in_array('Celsus_MultiTenant_Service_Reseller_Interface', class_implements($resellerClass))) {
				throw new Celsus_Exception("Reseller Class must implement Celsus_MultiTenant_Service_Reseller_Interface");
			}
			$this->_resellerClass = $resellerClass;
		}
	}

	/**
	 * Determines what the default module should be, based on the server name.
	 * If we are visiting a subsite, checks that the subsite is valid.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$frontController = Zend_Controller_Front::getInstance();

		$server = $_SERVER['HTTP_HOST'];

		// Check that the organisational and reseller accounts exist and match.
		$subdomains = explode('.', $server);
		$tenantDomain = $subdomains[0];

		// @todo Choose the username to bind to.
		$databaseAdapterName = $this->_databaseAdapterName;
		Zend_Registry::get('config')->database->$databaseAdapterName->connection->username = $tenantDomain;

		// Find the tenant and check that it exists.
		// @todo Probably try-catch this as if the username isn't valid, it will throw a database exception.
		$tenantClass = $this->_tenantClass;
		$tenant = $tenantClass::findBySubdomain($tenantDomain);
		if (!$tenant) {
			// Can't use an exception because this is pre-dispatch and the error handler has not been loaded.
			$request->setParam(Celsus_Error::ERROR_FLAG, Celsus_Error::EXCEPTION_NOT_FOUND);
			$request->setActionName('error')->setControllerName('error');
			return;
		}

		// If this is a white-labeled application, find the reseller and test that it exists.

		if (null !== $this->_resellerClass) {
			$resellerDomain = implode('.', array_slice($subdomains, 1));
			$resellerClass = $this->_resellerClass;

			$reseller = $resellerClass::findByDomain($resellerDomain);
			if (!$reseller) {
				// Can't use an exception because this is pre-dispatch and the error handler has not been loaded.
				$request->setParam(Celsus_Error::ERROR_FLAG, Celsus_Error::EXCEPTION_NOT_FOUND);
				$request->setActionName('error')->setControllerName('error');
			}
			// Store reseller information.
			Zend_Registry::set('reseller', $reseller);
		}

		// Store tenant information.
		Celsus_Application::setTenantName($tenantDomain);
		Zend_Registry::set('tenant', $tenant);
	}

}