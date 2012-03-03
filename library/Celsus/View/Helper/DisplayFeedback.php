<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: DisplayFeedback.php 72 2010-09-14 01:56:33Z jamie $
 */

/**
 * View helper to display any feedback, information or error messages
 * in a formatted box.
 *
 * @class Celsus_View_Helper_DisplayFeedback
 * @ingroup Celsus_View_Helpers
 */
class Celsus_View_Helper_DisplayFeedback extends Zend_View_Helper_Abstract {

	/**
	 * Displays feedback, information or error messages.
	 *
	 * @param string $type
	 * @return string
	 */
	public function displayFeedback($type = array()) {

		$feedbackGroups = Celsus_Feedback::get($type);
		if (!$feedbackGroups) {
			return;
		}

		$feedback = array();
		foreach ($feedbackGroups as $type => $feedbackItems) {
			foreach ($feedbackItems as $feedbackItem) {
				$item = new stdClass();
				$item->type = $type;
				$item->message = $feedbackItem['message'];
				$item->callback = $feedbackItem['callback'];
				$feedback[] = $item;
			}
		}
		$feedback = Zend_Json::encode($feedback);
		$this->view->getHelper('jQuery')->addJavascript("$.fn.displayFeedback.feedback = $feedback;");
	}
}
?>