<?php

class Celsus_Db_Engine_Document implements Celsus_Db_Engine_Interface {

	public static function factory($adapter, $config = array()) {
		if ($config instanceof Zend_Config) {
			$config = $config->toArray();
		}

		if (!is_array($config)) {
			throw new Celsus_Exception('Adapter parameters must be in an array or a Zend_Config object');
		}

		if (!is_string($adapter) || empty($adapter)) {
			throw new Celsus_Exception('Adapter name must be specified in a string');
		}

		// Get adapter class name.
		$adapterNamespace = 'Celsus_Db_Document_Adapter';
		if (isset($config['adapterNamespace'])) {
			if ($config['adapterNamespace'] != '') {
				$adapterNamespace = $config['adapterNamespace'];
			}
			unset($config['adapterNamespace']);
		}

		$adapterName = $adapterNamespace . '_';
		$adapterName .= str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($adapter))));

		// Create the new adapter
		return new $adapterName($config);
	}
}

?>