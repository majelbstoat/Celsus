<?php

class Celsus_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	/**
	 * The resources to exclude when bootstrapping.
	 *
	 * @var array
	 */
	protected $_excludedResources = array();

	public function getExcludedResources() {
		return $this->_excludedResources;
	}

	/**
	 * Sets the resources that should be excluded when bootstrapping.
	 * @return Celsus_Application_Bootstrap_Bootstrap
	 */
	public function setExcludedResources(array $excludedResources) {
		$this->_excludedResources = $excludedResources;
		return $this;
	}

	/**
	 * Bootstraps the application, and allows for excluding of resources.
	 *
	 * @param null|string|array $resource
	 */
		protected function _bootstrap($resource = null) {
		if (is_null($resource) && $this->_excludedResources) {
			$defaultResources = array_merge($this->getClassResourceNames(), $this->getPluginResourceNames());
			$resource = array_diff($defaultResources, $this->_excludedResources);
		}
		return parent::_bootstrap($resource);

	}

	/**
	 * Get the plugin loader for resources
	 *
	 * @return Zend_Loader_PluginLoader_Interface
	 */
	public function getPluginLoader()
	{
		if ($this->_pluginLoader === null) {
			$options = array(
				'Zend_Application_Resource'  => 'Zend/Application/Resource',
				'ZendX_Application_Resource' => 'ZendX/Application/Resource',
				//'Celsus_Application_Resource' => 'Celsus/Application/Resource'
			);

			$this->_pluginLoader = new Zend_Loader_PluginLoader($options);
		}

		return $this->_pluginLoader;
	}
}
