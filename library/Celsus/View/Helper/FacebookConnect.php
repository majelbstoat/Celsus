<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: DisplayFeedback.php 72 2010-09-14 01:56:33Z jamie $
 */

/**
 * View helper to allow an application to Connect to Facebook.
 *
 * @class Celsus_View_Helper_DisplayFeedback
 * @ingroup Celsus_View_Helpers
 */
class Celsus_View_Helper_FacebookConnect extends Zend_View_Helper_Abstract {

	/**
	 * Displays a facebook connect button.
	 *
	 * Relies on the following configuration:
	 *
	 * auth.facebook.scope.basic
	 * auth.facebook.callbackPath
	 * auth.facebook.applicationId
	 *
	 * @param string $type
	 * @return string
	 */
	public function facebookConnect($type = array()) {
		$requestUrl = Celsus_Routing::linkTo('auth_facebook_request', array('context' => 'connect'));
		?><a href="<?= $requestUrl ?>" id="facebook-connect"><img src="/i/facebook-connect.png" title="Connect With Facebook" alt="Connect With Facebook"></a>
		<?php
	}


}
?>
