<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: StagingWarning.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * View helper add warnings for development environments.
 *
 * @class Celsus_View_Helper_StagingWarning
 * @ingroup Celsus_View_Helpers
 */
class Celsus_View_Helper_StagingWarning extends Zend_View_Helper_Abstract {

	/**
	 * Displays staging warning if we are in staging mode.
	 *
	 * @param string $type
	 * @return string
	 */
	public function stagingWarning() {
		if ('development' == APPLICATION_ENV) {
			?>
			<div id="staging-debug"><?= str_repeat("Staging Environment&nbsp;&nbsp;&nbsp;&nbsp;", 4) ?></div>
			<?php
		}
	}
}
?>