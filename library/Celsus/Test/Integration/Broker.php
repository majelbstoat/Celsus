<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Test
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Broker.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Decorator object that provides a gateway for resetting the data for a known state.
 *
 * @category Celsus
 * @package Celsus_Test
 */
class Celsus_Test_Integration_Broker {

	protected $_enabled = true;

	protected $_seededSources = array();

	public function setEnabled($enabled) {
		$this->_enabled = $enabled;
	}

	public function reset() {
		if (!$this->_enabled) {
			return;
		}

		foreach ($this->_seededSources as $seededSource) {
			$seededSource->reset();
		}
	}

	public function seed($name, $adapterName = null) {

		if (!$this->_enabled) {
			return;
		}

		if (null === $adapterName) {
			$adapterName = Celsus_Db::getDefaultAdapterName();
		}

		$adapter = Celsus_Db::getAdapter($adapterName);
		$engine = Celsus_Db::getEngineType($adapterName);

		$sourceClass = 'Celsus_Test_Db_' . ucfirst($engine);

		$source = new $sourceClass($adapter);
		$source->load($name);

		$this->_seededSources[] = $source;
	}
}