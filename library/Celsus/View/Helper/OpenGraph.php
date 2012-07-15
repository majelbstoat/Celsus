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
 * @class Celsus_View_Helper_FacebookConnect
 * @ingroup Celsus_View_Helpers
 */
class Celsus_View_Helper_FacebookConnect extends Celsus_View_Helper {

	/**
	 * Displays a facebook connect button.
	 *
	 * @param string $type
	 * @return string
	 */
	public function render() {
		$requestUrl = Celsus_Routing::linkTo('auth_facebook_request', array('context' => 'connect'));
		?><a href="<?= $requestUrl ?>" id="facebook-connect"><img src="/i/facebook-connect.png" title="Connect With Facebook" alt="Connect With Facebook"></a>
		<?php
	}


}
?>
