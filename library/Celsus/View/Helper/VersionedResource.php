<?php

class Celsus_View_Helper_VersionedResource {

		const RESOURCE_LIST = 'configs/resources.ini';
		protected $_view;

		/**
		 * An array of resources that are under version control for caching.
		 * @var Zend_Config_Ini
		 */
		protected static $_resources = null;

    public function setView(Zend_View_Interface $view) {
        $this->_view = $view;
    }

    public static function setupResources() {
    	self::$_resources = new Zend_Config_Ini(APPLICATION_PATH . '/' . self::RESOURCE_LIST);
    }

    /**
     * Returns the URL to a static file, prepended with the base URL,
     * injecting a version number so we can aggressively cache resources.
     *
     * @param string $resource
     * @return string
     */
    public function versionedResource($resource) {
    	if (Celsus_Application::isDevelopment()) {
    		return $this->_view->baseUrl() . '/' . $resource;
    	}

    	if (null === self::$_resources) {
    		self::setupResources();
    	}

    	list($path, $extension) = explode('.', $resource, 2);
    	$subSections = explode('/', $path);
    	try {
    		$versionedResource = self::$_resources;
    		foreach ($subSections as $subSection) {
    			if (!isset($versionedResource->$subSection)) {
    				$versionedResource = null;
    				break;
    			}
    			$versionedResource = $versionedResource->$subSection;
    		}
    		if (null == $versionedResource) {
    			throw new Celsus_Exception("$resource cannot be versioned!");
    		}
    		$version = $versionedResource;
    	} catch (Exception $e) {
    		// Can't version this file, so just return the prepended resource.
    		return $this->_view->baseUrl() . '/' . $resource;
    	}
      return $this->_view->baseUrl() . '/' . preg_replace('/\.([a-z]+?)$/', ".v$version.\$1", $resource);
    }
}
?>