<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Cache
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: TaggedMemcached.php 69 2010-09-08 12:32:03Z jamie $
 */
require_once 'Zend/Cache/Backend/Memcached.php';
/**
 * Memcached backend that provides tagging.
 *
 * @category Celsus
 * @package Celsus_Cache
 */
class Celsus_Cache_Backend_TaggedMemcached extends Zend_Cache_Backend_Memcached implements Zend_Cache_Backend_ExtendedInterface {

	const TAG_CACHE_KEY_BASE = '__tags__';

	const SHARED_TAG_CACHE = 'shared';

	const TENANT_TAG_CACHE = 'tenant';

	protected $_tagData = array();

	public function getIds() {
		$tagData = $this->_getTagData();
		$return = array();
		foreach ($tagData as $tag => $ids) {
			$return = array_merge($return, $ids);
		}
		return array_unique($return);
	}

	/**
	 * Return an array of stored tags
	 *
	 * @return array array of stored tags (string)
	 */
	public function getTags() {
		$tagData = $this->_getTagData();
		return array_keys($tagData);
	}

	protected function _getTagData() {
		$shared = Celsus_Cache_Manager::isShared();
		$tagCache = $shared ? self::SHARED_TAG_CACHE : self::TENANT_TAG_CACHE;
		if (!array_key_exists($tagCache, $this->_tagData)) {
			$key = $this->_getTagCacheKey();
			$data = $this->_memcache->get($key);
			if (!$data) {
				$data = array();
			}
			$this->_tagData[$tagCache] = $data;
		}
		return $this->_tagData[$tagCache];
	}

	protected function _saveTagData($tagData) {
		$shared = Celsus_Cache_Manager::isShared();
		$tagCache = $shared ? self::SHARED_TAG_CACHE : self::TENANT_TAG_CACHE;
		$key = $this->_getTagCacheKey();
		$this->_tagData[$tagCache] = $tagData;
		$this->_memcache->set($key, $tagData, 0, 0);
	}

	protected function _getTagCacheKey() {
		return Celsus_Cache_Manager::multiTenantKey(self::TAG_CACHE_KEY_BASE);
	}

	public function save($data, $id, $tags = array(), $specificLifetime = false) {
		$lifetime = $this->getLifetime($specificLifetime);
		if ($this->_options['compression']) {
			$flag = MEMCACHE_COMPRESSED;
		} else {
			$flag = 0;
		}

		// ZF-8856: using set because add needs a second request if item already exists
		$result = @$this->_memcache->set($id, array($data, time(), $lifetime), $flag, $lifetime);

		if (count($tags) > 0) {
			$tagData = $this->_getTagData();
			foreach ($tags as $tag) {
				$tagData[$tag][] = $id;
			}
			$this->_saveTagData($tagData);
		}
		return $result;
	}

	/**
	 * Return an array of stored cache ids which match given tags
	 *
	 * In case of multiple tags, a logical AND is made between tags
	 *
	 * @param array $tags array of tags
	 * @return array array of matching cache ids (string)
	 */
	public function getIdsMatchingTags($tags = array()) {
		$tagData = $this->_getTagData();

		$matchingTags = array_intersect_key($tagData, array_flip($tags));
		$removableKeys = array_pop($potentials);

		foreach ($matchingTags as $tag => $keys) {
			$removableKeys = array_intersect($removableKeys, $keys);
		}

		return $removableKeys;
	}

	/**
	 * Return an array of stored cache ids which don't match given tags
	 *
	 * In case of multiple tags, a logical OR is made between tags
	 *
	 * @param array $tags array of tags
	 * @return array array of not matching cache ids (string)
	 */
	public function getIdsNotMatchingTags($tags = array()) {
		// Not implented.
		return array();
	}

	/**
	 * Return an array of stored cache ids which match any given tags
	 *
	 * In case of multiple tags, a logical AND is made between tags
	 *
	 * @param array $tags array of tags
	 * @return array array of any matching cache ids (string)
	 */
	public function getIdsMatchingAnyTags($tags = array()) {
		$tagData = $this->_getTagData();

		$matchingTags = array_intersect_key($tagData, array_flip($tags));

		$return = array();
		foreach ($matchingTags as $tag => $keys) {
			$return = array_merge($return, $keys);
		}

		return array_unique($return);
	}

	/**
	 * Tag support is provided.
	 *
	 * @return array associative of with capabilities
	 */
	public function getCapabilities() {
		return array(
			'automatic_cleaning' => false,
			'tags' => true,
			'expired_read' => false,
			'priority' => false,
			'infinite_lifetime' => false,
			'get_list' => false
		);
	}

	protected function _clean($keys) {
		$tagData = $this->_getTagData();

		foreach ($keys as $key) {
			$this->_memcache->delete($key);
		}

		foreach (array_keys($tagData) as $tag) {
			$tagData[$tag] = array_diff($tagData[$tag], $keys);
			if (!$tagData[$tag]) {
				unset($tagData[$tag]);
			}
		}
		$this->_saveTagData($tagData);

	}

	public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array()) {
		switch ($mode) {
			case Zend_Cache::CLEANING_MODE_ALL:
				return $this->_memcache->flush();
				break;

			case Zend_Cache::CLEANING_MODE_OLD:
				$this->_log("Zend_Cache_Backend_Memcached::clean() : CLEANING_MODE_OLD is unsupported by the Memcached backend");
				break;

			case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
				$removableKeys = $this->getIdsMatchingAnyTags($tags);
				$this->_clean($removableKeys);
				break;

			case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
				$removableKeys = $this->getIdsMatchingTags($tags);
				$this->_clean($removableKeys);
				break;

			case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
				$this->_log(self::TAGS_UNSUPPORTED_BY_CLEAN_OF_MEMCACHED_BACKEND);
				break;
			default:
				Zend_Cache::throwException('Invalid mode for clean() method');
				break;
		}
	}
}