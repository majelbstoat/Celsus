<?php

class Celsus_Data_Marshal_FacebookDocument extends Celsus_Data_Marshal {

	protected static $_marshalledClass = 'Celsus_Db_Document_Facebook';

	public static function provide($object) {
		if (!$object instanceof Celsus_Db_Document_Facebook) {
			throw new Celsus_Exception("Must implement Celsus_Db_Document_Facebook");
		}
		$data = $object->toArray();
		$id = $data['id'];
		unset($data['id']);

		return array($id, $data, array());
	}

	public static function save(array $data, $object) {
		if (!$object instanceof Celsus_Db_Document_Facebook) {
			throw new Celsus_Exception("Must implement Celsus_Db_Document_Facebook");
		}
		return $object->setFromArray($data)->save();
	}
}