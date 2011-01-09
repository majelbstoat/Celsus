<?php

abstract class Celsus_Model_Rowset extends Zend_Db_Table_Rowset_Abstract {
	
	/**
	 * Returns an array of the IDs contained in this rowset.
	 * @return array
	 */
	public function getIdentities() {
		$primary = $this->_table->info(Zend_Db_Table_Abstract::PRIMARY);
		$primaryCount = count($primary);
		foreach ($this->_data as $data) {
			if ($primaryCount > 1) {
				foreach ($primary as $column) {
					$values[$column] = $data[$column];
				}
				$return[] = $values;
			} else {
				$column = current($primary);
				$return[] = $data[$column];
			}
		}
		return $return;
	}
	
	/**
	 * Prepares a table reference for lookup.
	 *
	 * Ensures all reference keys are set and properly formatted.
	 *
	 * @param Zend_Db_Table_Abstract $dependentTable
	 * @param Zend_Db_Table_Abstract $parentTable
	 * @param string                 $ruleKey
	 * @return array
	 */
	protected function _prepareReference(Zend_Db_Table_Abstract $dependentTable, Zend_Db_Table_Abstract $parentTable, $ruleKey) {
		$map = $dependentTable->getReference(get_class($parentTable), $ruleKey);
		
		if (!isset($map[Zend_Db_Table_Abstract::REF_COLUMNS])) {
			$parentInfo = $parentTable->info();
			$map[Zend_Db_Table_Abstract::REF_COLUMNS] = array_values((array) $parentInfo['primary']);
		}
		
		$map[Zend_Db_Table_Abstract::COLUMNS] = (array) $map[Zend_Db_Table_Abstract::COLUMNS];
		$map[Zend_Db_Table_Abstract::REF_COLUMNS] = (array) $map[Zend_Db_Table_Abstract::REF_COLUMNS];
		
		return $map;
	}
	
	public function findDependentRowset($dependentTable, $ruleKey = null, Zend_Db_Table_Select $select = null) {
		$db = $this->getTable()->getAdapter();
		
		if (is_string($dependentTable)) {
			try {
				@Zend_Loader::loadClass($dependentTable);
			} catch (Zend_Exception $e) {
				require_once 'Zend/Db/Table/Row/Exception.php';
				throw new Zend_Db_Table_Row_Exception($e->getMessage());
			}
			$dependentTable = new $dependentTable(array ('db' => $db));
		}
		if (!$dependentTable instanceof Zend_Db_Table_Abstract) {
			$type = gettype($dependentTable);
			if ($type == 'object') {
				$type = get_class($dependentTable);
			}
			require_once 'Zend/Db/Table/Row/Exception.php';
			throw new Zend_Db_Table_Row_Exception("Dependent table must be a Zend_Db_Table_Abstract, but it is $type");
		}
		
		if ($select === null) {
			$select = $dependentTable->select();
		} else {
			$select->setTable($dependentTable);
		}
		
		$map = $this->_prepareReference($dependentTable, $this->getTable(), $ruleKey);
		
		for($i = 0; $i < count($map[Zend_Db_Table_Abstract::COLUMNS]); ++$i) {
			$parentColumnName = $db->foldCase($map[Zend_Db_Table_Abstract::REF_COLUMNS][$i]);
			$parentColumnNames[] = $parentColumnName;
			// Use adapter from dependent table to ensure correct query construction
			$dependentDb = $dependentTable->getAdapter();
			$dependentColumnName = $dependentDb->foldCase($map[Zend_Db_Table_Abstract::COLUMNS][$i]);
			$dependentColumn = $dependentDb->quoteIdentifier($dependentColumnName, true);
			$dependentInfo = $dependentTable->info();
			$dependentColumnsQuoted[] = $dependentColumn;
			$dependentColumns[] = $dependentColumnName;
		}
		
		foreach ($this->_data as $data) {
			$buffer = array ();
			foreach ($parentColumnNames as $parentColumnName) {
				$buffer[] = $db->quote($data[$parentColumnName]);
			}
			$values[] = '(' . implode(', ', $buffer) . ')';
		}
		$dependentColumnsString = '(' . implode(', ', $dependentColumnsQuoted) . ')';
		$values = implode(', ', $values);
		$select->where("($dependentColumnsString) IN ($values)");
		$results = $dependentTable->fetchAll($select);
		
		// Finally, key the results on the primary key.
		foreach ($results as $result) {
			$keyComponents = array ();
			foreach ($dependentColumns as $dependentColumn) {
				$keyComponents[] = $result->$dependentColumn;
			}
			$key = implode(',', $keyComponents);
			$return[$key][] = $result;
		}
		return $return;
	}

}

?>