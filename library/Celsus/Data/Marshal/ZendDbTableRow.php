<?php

class Celsus_Data_Marshal_ZendDbTableRow  implements Celsus_Data_Marshal_Interface {

	protected static $_marshalledClass = 'Zend_Db_Table_Row_Abstract';

	public static function provides() {
		return self::$_marshalledClass;
	}

	public static function provide($object) {
		if (!$object instanceof Zend_Db_Table_Row_Abstract) {
			throw new Celsus_Exception("Must implement Zend_Db_Table_Row_Abstract");
		}
		return $object->toArray();
	}

	public static function save(array $data, $object) {
		if (!$object instanceof Zend_Db_Table_Row_Abstract) {
			throw new Celsus_Exception("Must implement Zend_Db_Table_Row_Abstract");
		}
		return $object->setFromArray($data)->save();
	}
}

?>