<?php

class Celsus_Data_Marshal_CouchDocument extends Celsus_Data_Marshal {

	protected static $_marshalledClass = 'Celsus_Db_Document_Couch';

	public static function provide($object) {
		if (!$object instanceof Celsus_Db_Document_Couch) {
			throw new Celsus_Exception("Must implement Celsus_Db_Document_Couch");
		}
		$data = $object->toArray();
		if (array_key_exists('doc', $data)) {
			$data = $data['doc'];
		}
		$id = $data['_id'];
		unset($data['_id']);

		// Revision and document type are implementation details that should be hidden from the application layer.
		unset($data['type']);
		unset($data['_rev']);

		return array($id, $data, array());
	}

	public static function save(array $data, $object) {
		if (!$object instanceof Celsus_Db_Document_Couch) {
			throw new Celsus_Exception("Must implement Celsus_Db_Document_Couch");
		}
		$data['_id'] = $data['id'];
		unset($data['id']);
		return $object->setFromArray($data)->save();
	}
}

?>