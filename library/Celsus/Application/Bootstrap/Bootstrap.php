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
			$resource = array_diff($this->getClassResourceNames(), $this->_excludedResources);
		}
		return parent::_bootstrap($resource);

	}


}