<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: VersionedResource.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Versions a static resource based on file modification time or version number.
 *
 * @class Celsus_View_Helper_VersionedResource
 * @ingroup Celsus_View_Helpers
 */
class Celsus_View_Helper_VersionedResource extends Zend_View_Helper_Abstract {

    /**
     * Returns the URL to a static file, prepended with the base URL,
     * injecting a version number so we can aggressively cache resources.
     *
     * @param string $resource
     * @return string
     */
    public function versionedResource($resource) {
    	return Celsus_Resource::version($resource);
    }
}
?>