<?php

class Celsus_Data_Marshal_ZendDbTableRow extends Celsus_Data_Marshal {

	protected static $_marshalledClass = 'Zend_Db_Table_Row_Abstract';

	public static function provide($object) {
		if (!$object instanceof Zend_Db_Table_Row_Abstract) {
			throw new Celsus_Exception("Must implement Zend_Db_Table_Row_Abstract");
		}
		$data = $object->toArray();
		$id = $data['id'];
		unset($data['id']);

		return array($id, $data, array());
	}

	public static function save(array $data, $object) {
		if (!$object instanceof Zend_Db_Table_Row_Abstract) {
			throw new Celsus_Exception("Must implement Zend_Db_Table_Row_Abstract");
		}
		return $object->setFromArray($data)->save();
	}
}