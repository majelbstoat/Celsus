<?php

class Celsus_Feedback {

	protected static $_feedback = null;
	
	const INFO = 'info';
	const ERROR = 'error';
		
	/**
	 * Adds a message to the feedback stack and preserves it across the current session.
	 *
	 * @param string $type
	 * @param string|int $message
	 */
	public static function add($type, $message) {
		if (null == self::$_feedback) {
			self::$_feedback = new Zend_Session_Namespace('Feedback');
		}
		
		switch ($type) {
			case Celsus_Feedback::INFO:
			case Celsus_Feedback::ERROR:
				self::$_feedback->{$type}[] = $message;				
				break;
							
			default:
				throw new Celsus_Exception('Invalid type "' . $type . '" specified.');
		}
	}
	
	/**
	 * Adds unacknowledged system notices for the current user.
	 *
	 * @todo Implement this class.
	 * @param int|array $messageId
	 */
	public static function addSystemNotices() {
		$model = new BAM_Model_Notice();
		if ($messages = $model->findUnacknowledged()) {
			
		}
	}
	
	/**
	 * Gets the currently buffered feedback.
	 *
	 * @param unknown_type $type
	 * @return unknown
	 */
	public static function get($type, $clean = true) {
		if (null == self::$_feedback) {
			self::$_feedback = new Zend_Session_Namespace('Feedback');
		}
		
		switch ($type) {
			case Celsus_Feedback::INFO:
			case Celsus_Feedback::ERROR:
				$return = self::$_feedback->$type ? self::$_feedback->$type : array();
				if (true === $clean) {
					unset(self::$_feedback->$type);
				}
				return $return;
				
			default:
				throw new Celsus_Exception('Invalid type "' . $type .'" specified.');
		}		
	}
	
}
?>