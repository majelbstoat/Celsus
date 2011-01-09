<?php

/**
 * View helper to display any feedback, information or error messages
 * in a formatted box.
 *
 */
class Celsus_View_Helper_DisplayFeedback {
	
	protected $_view;

	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
	}

	/**
	 * Displays feedback, information or error messages.
	 *
	 * @param string $type
	 * @return string
	 */
	public function displayFeedback($type) {
		$feedback = Celsus_Feedback::get($type, true);
		if (!$feedback) {
			return '';
		}
		ob_start();
		if (Celsus_Feedback::ERROR == $type) {			
			$feedback[0] = '<strong>Error:</strong> ' . $feedback[0];
		}
		?>
		<div class="feedback <?= $type ?>">
		<p><?= implode('</p><p>', $feedback) ?></p>
		</div>
		<?php
		return ob_get_clean();
	}
}
?>