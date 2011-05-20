<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Session
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Cache.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Provides a cached backed for sessions.
 *
 * @category Celsus
 * @package Celsus_Session
 */
class Celsus_Session_SaveHandler_Cache implements Zend_Session_SaveHandler_Interface {

	protected $_sessionCacheName = 'default';

	/**
	 * Open Session - retrieve resources
	 *
	 * @param string $save_path
	 * @param string $name
	 */
	public function open($save_path, $name) {
		return true;
	}

	/**
	 * Close Session - free resources
	 *
	 */
	public function close() {
		return true;
	}

	/**
	 * Read session data
	 *
	 * @param string $id
	 */
	public function read($id) {
		return Celsus_Cache_Manager::cache($this->_sessionCacheName)->load($id);
	}

	/**
	 * Write Session - commit data to resource
	 *
	 * @param string $id
	 * @param mixed $data
	 */
	public function write($id, $data) {
		Celsus_Cache_Manager::cache($this->_sessionCacheName)->save($data, $id, array('session'));
		return true;
	}

	/**
	 * Destroy Session - remove data from resource for
	 * given session id
	 *
	 * @param string $id
	 */
	public function destroy($id) {
		return Celsus_Cache_Manager::cache($this->_sessionCacheName)->delete($id);
	}

	/**
	 * Garbage Collection - remove old session data older
	 * than $maxlifetime (in seconds)
	 *
	 * @param int $maxlifetime
	 */
	public function gc($maxlifetime) {
		// No garbage collection necessary for cached backend.
		return true;
	}

}