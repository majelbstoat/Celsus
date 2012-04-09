<?php

class Celsus_Db_Engine_Relational implements Celsus_Db_Engine_Interface {

	public static function factory($adapter, $config = array()) {
		$adapter = Zend_Db::factory($adapter, $config);

		if (Celsus_Db::hasProfiling()) {
			$adapter->setProfiler(Celsus_Db::getProfiler());
		}

		$adapter->setFetchMode(Zend_Db::FETCH_OBJ);
		return $adapter;

	}
}