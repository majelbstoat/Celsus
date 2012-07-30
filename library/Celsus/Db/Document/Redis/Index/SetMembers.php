<?php

class Celsus_Db_Document_Redis_Index_SetMembers implements Celsus_Db_Document_Redis_Index_Interface {

	public function update($id, array $config, Redis $pipeline) {
		$data = $config['new'];
		$originalData = $config['old'];
		$field = $config['field'];
		$fieldName = implode('', array_map('ucfirst', explode('_', $field)));

		// If the value is new, or has been updated, write an index.
		if ($data[$field] && ($data[$field] != $originalData[$field])) {
			$key = $config['group'] . ':membersBy' . $fieldName . ':' . $data[$field];
			$pipeline->sAdd($key, $id);
		}

		// If we had data already and the data has changed, delete the old index entry.
		if ($originalData[$field] && ($originalData[$field] != $data[$field])) {
			$key = $config['group'] . ':membersBy' . $fieldName . ':' . $originalData[$field];
			$pipeline->sRem($key, $id);
		}
	}
}