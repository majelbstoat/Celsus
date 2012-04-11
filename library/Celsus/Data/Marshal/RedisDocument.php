<?php

class Celsus_Data_Marshal_RedisDocument implements Celsus_Data_Marshal_Interface {

	protected static $_marshalledClass = 'Celsus_Db_Document_Redis';

	public static function provides() {
		return self::$_marshalledClass;
	}

	public static function provide($object) {
		if (!$object instanceof Celsus_Db_Document_Redis) {
			throw new Celsus_Exception("Must implement Celsus_Db_Document_Redis");
		}
		$data = $object->toArray();
		unset($data['_created']);
		unset($data['_type']);

		return array($data['id'], $data);
	}

	public static function save(array $data, $object) {
		if (!$object instanceof Celsus_Db_Document_Redis) {
			throw new Celsus_Exception("Must implement Celsus_Db_Document_Redis");
		}
		return $object->setFromArray($data)->save();
	}
}

?>