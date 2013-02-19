<?php

class Celsus_Data_Marshal_RedisDocument extends Celsus_Data_Marshal {

	protected static $_marshalledClass = 'Celsus_Db_Document_Redis';

	public static function provide($object) {
		if (!$object instanceof Celsus_Db_Document_Redis) {
			throw new Celsus_Exception("Must implement Celsus_Db_Document_Redis");
		}
		$data = $object->toArray();

		$metadata = array(
			'_created' => $data['_created'],
			'_type' => $data['_type']
		);

		unset($data['_created']);
		unset($data['_type']);

		return array($data['id'], $data, $metadata);
	}

	public static function save(array $data, $object) {
		if (!$object instanceof Celsus_Db_Document_Redis) {
			throw new Celsus_Exception("Must implement Celsus_Db_Document_Redis");
		}
		return $object->setFromArray($data)->save();
	}
}