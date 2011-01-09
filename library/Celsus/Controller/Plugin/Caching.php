<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Caching.php 49 2010-07-18 23:23:29Z jamie $
 */

/**
 * Caches GET requests that aren't redirects
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Plugin_Caching extends Zend_Controller_Plugin_Abstract {

	/**
	 * Whether or not to disable caching
	 *
	 * @var bool
	 */
	protected static $_disableCache = false;

	/**
	 * The MD5-hashed key for the cache lookup and store.
	 *
	 * @var string Cache key
	 */
	protected $_key;

	/**
	 * Whether or not to suppress exiting when we find a cache hit.
	 *
	 * @var bool
	 */
	protected $_suppressExit = false;

	/**
	 * Start caching
	 *
	 * Determine if we have a cache hit. If so, return the response; else,
	 * start caching.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		if (!$request->isGet()) {
			self::$_disableCache = true;
			return;
		}

		$path = $request->getPathInfo();

		$this->_key = md5($path);
		$response = Celsus_Cache::load($this->_key);
		if (false !== $response) {
			$response->sendResponse();
			if (!$this->_suppressExit) {
				exit();
			}
		}
	}

	/**
	 * Store cache
	 *
	 * @return void
	 */
	public function dispatchLoopShutdown() {
		if (self::$_disableCache || $this->getResponse()->isRedirect() || (null === $this->_key)) {
			// We can't or don't want to cache this page.
			return;
		}
		// Cache this page.
		Celsus_Cache::save($this->getResponse(), $this->_key);
	}

	public function suppressExit($suppressExit) {
		$this->_suppressExit = $suppressExit;
	}
}
