<?php

class Celsus_Db_Engine_Mock implements Celsus_Db_Engine_Interface {

	public static function factory($adapter, $config = array()) {
		// Get adapter class name.
		$adapterNamespace = 'Celsus_Db_Mock_Adapter';
		if (isset($config['adapterNamespace'])) {
			if ($config['adapterNamespace'] != '') {
				$adapterNamespace = $config['adapterNamespace'];
			}
			unset($config['adapterNamespace']);
		}

		$adapterName = $adapterNamespace . '_' . ucfirst($adapter);

		// Create the new adapter
		return new $adapterName($config);
	}
}