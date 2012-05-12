<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Resource.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Functionality pertaining to static HTTP-deployed resources.
 *
 * @defgroup Celsus_Resource Celsus Resource
 */

/**
 * Provides resource versioning capabilities.
 *
 * @ingroup Celsus_Resource
 */
class Celsus_Resource {

	const RESOURCE_LIST = 'configs/resources.ini';

	/**
	 * An array of resources that are under version control for caching.
	 * @var Zend_Config_Ini
	 */
	protected static $_resources = null;

	public static function setupResources() {
		if (null === self::$_resources) {
			self::$_resources = new Zend_Config_Ini(APPLICATION_PATH . '/' . self::RESOURCE_LIST);
		}
	}

	/**
	 * Returns the URL to a static file, prepended with the base URL,
	 * injecting a version number so we can aggressively cache resources.
	 *
	 * @param string $resource
	 * @return string
	 */
	public static function version($resource) {
		if (Celsus_Application::isDevelopment()) {
			$version = filemtime(PUBLIC_PATH . DIRECTORY_SEPARATOR . $resource);
		} else {
			self::setupResources();
			list($path, $extension) = explode('.', $resource, 2);
			$subSections = explode('/', $path);

			$versionedResource = self::$_resources;
			foreach ($subSections as $subSection) {
				if (!isset($versionedResource->$subSection)) {
					$versionedResource = null;
					break;
				}
				$versionedResource = $versionedResource->$subSection;
			}
			if (null == $versionedResource) {
				return Celsus_Application::tenantUrl() . '/' . $resource;
			}
			$version = $versionedResource;
		}
		return Celsus_Application::tenantUrl() . '/' . preg_replace('/\.([a-z]+?)$/', ".v$version.\$1", $resource);
	}
}