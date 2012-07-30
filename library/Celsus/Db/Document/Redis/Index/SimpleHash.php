<?php

class Celsus_Db_Document_Redis_Index_SimpleHash implements Celsus_Db_Document_Redis_Index_Interface {

	public function update($id, array $config, Redis $pipeline) {
		$data = $config['new'];
		$originalData = $config['old'];
		$field = $config['field'];
		$key = $config['group'] . ':by' . implode('', array_map('ucfirst', explode('_', $field)));

		// If the value is new, or has been updated, write an index.
		if ($data[$field] && ($data[$field] != $originalData[$field])) {
			$hashField = $data[$field];
			$pipeline->hSet($key, $hashField, $id);
		}

		// If we had data already and the data has changed, delete the old index entry.
		if ($originalData[$field] && ($originalData[$field] != $data[$field])) {
			$hashField = $originalData[$field];
			$pipeline->hDel($key, $hashField);
		}
	}
}