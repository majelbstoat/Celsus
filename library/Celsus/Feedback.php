<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Feedback.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Feedback functionality
 *
 * @defgroup Celsus_Feedback Celsus Feedback
 */

/**
 * Provides a session-based feedback mechanism that persists across
 * application requests.
 *
 * @ingroup Celsus_Feedback
 */
class Celsus_Feedback {

	const INFO = 'info';
	const WARNING = 'warning';
	const ERROR = 'error';

	const FEEDBACK_INVALID_ACTION = 'invalidControllerAction';
	const FEEDBACK_AUTHORISATION_REQUIRED = 'authorisationRequired';

	const CONFIG_FEEDBACK = 'feedback.yaml';
	const CONFIG_MESSAGE_TYPE = 'feedback';

	protected static $_session = null;

	protected static $_definitions = null;

	protected static $_messages = null;

	/**
	 * Gets the session object.
	 *
	 * @return Zend_Session_Namespace
	 */
	protected static function _ensureSession() {
		if (null === self::$_session) {
			self::$_session = new Zend_Session_Namespace('Feedback');
		}
	}

	/**
	 * Gets the definition of the specified feedback.
	 *
	 * @todo Cache this
	 * return Zend_Config_Yaml
	 */
	protected static function _getFeedbackDefinition($code) {
		if (null === self::$_definitions) {
			self::$_definitions = new Zend_Config_Yaml(CONFIG_PATH . '/' . self::CONFIG_FEEDBACK);
		}

		return self::$_definitions->$code;
	}

	protected static function _getFeedbackMessage($code) {
		$messages = Celsus_I18n::getMessages(self::CONFIG_MESSAGE_TYPE);
		return $messages->$code;
	}

	/**
	 * Adds a message to the feedback stack and preserves it across the current session.
	 *
	 * @param string $code The application feedback code pertaining to an entry in feedback.yaml
	 * @param array $data The data to be replaced into the message.
	 * @param string $callback The URL to PUT to to acknowledge the message.
	 */
	public static function add($code, array $data = array(), $callback = null) {
		self::_ensureSession();

		$feedback = self::_getFeedbackDefinition($code);

		switch ($feedback->type) {
			case Celsus_Feedback::INFO:
			case Celsus_Feedback::WARNING:
			case Celsus_Feedback::ERROR:
				$message = vsprintf(self::_getFeedbackMessage($code), $data);
				self::$_session->{$feedback->type}[] = array(
					'code' => $code,
					'message' => $message,
					'callback' => $callback
				);
				break;

			default:
				throw new Celsus_Exception('Invalid type "' . $feedback->type . '" specified.');
		}
	}

	public static function clean() {
		Zend_Session_Namespace::resetSingleInstance('Feedback');
		self::$_session = null;
	}

	/**
	 * Checks that the specified feedback has been buffered.
	 *
	 * @param string $code
	 * @return boolean
	 */
	public static function has($code) {
		$feedback = self::_getFeedbackDefinition($code);
		$type = $feedback->type;

		// Test that the type of feedback exists.
		if (!isset(self::$_session->$type)) {
			return false;
		}

		foreach (self::$_session->$type as $feedback) {
			if ($feedback['code'] == $code) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets the currently buffered feedback.
	 *
	 * @param string|array $types
	 * @param boolean $clean Whether or not to clear the feedback so it doesn't appear again.
	 * @return array()
	 */
	public static function get($types = array(), $clean = true) {
		self::_ensureSession();

		if (!is_array($types)) {
			$types = array($types);
		}
		if (!count($types)) {
			$types = array(
				Celsus_Feedback::INFO,
				Celsus_Feedback::WARNING,
				Celsus_Feedback::ERROR,
			);
		}

		$return = array();
		foreach ($types as $type) {
			switch ($type) {
				case Celsus_Feedback::INFO:
				case Celsus_Feedback::WARNING:
				case Celsus_Feedback::ERROR:
					if (isset(self::$_session->$type) && self::$_session->$type) {
						$return[$type] = self::$_session->$type;
					}
					if (true === $clean) {
						unset(self::$_session->$type);
					}
					break;

				default:
					throw new Celsus_Exception('Invalid type "' . $type .'" specified.');
			}
		}
		return $return;
	}
}
