<?php

class Celsus_Lookup {

	const DEFAULT_COLUMN = 'name';
	
	protected static $_cache = null;

	/**
	 * Returns data in a format suitable for populating a select box and caches the columns.
	 *
	 * @param string $table
	 * @param string $field
	 * @param string $database
	 * @return array
	 */
	public static function getOptions($table, $field = self::DEFAULT_COLUMN, $database = null) {
		return self::lookupAndCache($table, null, $field, $database);
	}
	
	/**
	 * Returns a value from a database lookup table, but doesn't cache the results.
	 *
	 * @param string $table
	 * @param int|string $search
	 * @param string $field
	 * @param string $database
	 * @return int|string|array
	 */
	public static function lookup($table, $search = null, $field = self::DEFAULT_COLUMN, $database = null) {
		if (!$search) {
			return null;
		}

		if (self::isCached($table, $field)) {
			// Return the value from the cache.
			return self::lookupFromCache($table, $search, $field);
		} else {
			// Perform the lookup from the database.
			if (is_int($search)) {
				$supplied = 'id';
				$requested = $field;
			} else {
				$supplied = $field;
				$requested = 'id';
			}
			if (null == $database) {
				$database = Celsus_Db::getDefaultAdapterName();
			}
			$db = Celsus_Db::getAdapter($database);
			if (is_array($search)) {
				$result = $db->fetchAll("SELECT $supplied, $requested FROM $table WHERE $supplied = '$search'");
				$return = array();
				foreach ($result as $row) {
					$return[$result->$supplied] = $result->$requested;
				}
				return $return;
			} else {
				return $db->fetchOne("SELECT $requested FROM $table WHERE $supplied = '$search' LIMIT 1");
			}
		}
	}
	
	/**
	 * Checks to see if the specified column in the specified table is already cached.
	 * 
	 * @param string $table
	 * @param string $field
	 * @return boolean
	 */
	public static function isCached($table, $field) {
		$cached = false;
		if (isset(self::$_cache[$table])) {
			$row = reset(self::$_cache[$table]);
			if (isset($row[$field])) {
				$cached = true;
			}
		}		
		return $cached;
	}

	/**
	 * Looks data up from the internal cache, without going to the database.
	 * 
	 * @param string $table
	 * @param string $search
	 * @param string $field
	 * @return mixed
	 */
	private static function lookupFromCache($table, $search, $field) {
		if (count($search) > 1) {
			foreach ($search as $value) {
				if (is_int($value)) {
					// Supplied an ID.
					$return[$value] = (isset(self::$_cache[$table][$value])) ? self::$_cache[$table][$value][$field] : null;
				} else {
					foreach (self::$_cache[$table] as $id => $entry) {
						if ($entry[$field] == $value) {
							$return[$value] = $id;
						}
					}
				}
			}
			return $return;
		} elseif (null !== $search) {
			if (is_int($search)) {
				return (isset(self::$_cache[$table][$search])) ? self::$_cache[$table][$search][$field] : null;
			} else {
				foreach (self::$_cache[$table] as $id => $entry) {
					if ($entry[$field] == $search) {
						return $id;
					}
				}
				return null;
			}
		} else {
			// By default, return all the cached data.
			foreach (self::$_cache[$table] as $id => $entry) {
				$return[$id] = $entry[$field];
			}
			return $return;
		}		
	}

	/**
	 * Retrieves and caches information from the database.
	 * 
	 * @param string $table
	 * @param string $field
	 * @param string $database
	 */
	public static function cache($table, $field, $database) {
		if (null == $database) {
			$database = Celsus_Db::getDefaultAdapterName();
		}
		$data = Celsus_Db::getAdapter($database)->fetchAll("SELECT id, $field FROM $table");
		foreach($data as $row) {
			self::$_cache[$table][$row->id][$field] = $row->$field;
		}
	}
	
	/**
	 * Returns a value from a database lookup table and caches the table for
	 * future use in this session.
	 *
	 * @param string $table
	 * @param int|string $search
	 * @param string $name
	 * @param string $database
	 * @return int|string|array
	 */
	public static function lookupAndCache($table, $search = null, $field = self::DEFAULT_COLUMN, $database = null) {
		if (!self::isCached($table, $field)) {
			self::cache($table, $field, $database);
		}
		return self::lookupFromCache($table, $search, $field);
	}
}

?>