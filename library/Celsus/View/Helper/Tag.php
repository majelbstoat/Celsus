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
 * @class Celsus_View_Helper_Tag
 * @ingroup Celsus_View_Helpers
 */
class Celsus_View_Helper_Tag extends Celsus_View_Helper {

	protected $_tags = array();

	protected $_selfClosing = array(
		'link',
		'meta'
	);

	protected function _tags() {
		$selfClosing = array_flip($this->_selfClosing);
		foreach ($this->_tags as $tagName => $tags) {
			foreach ($tags as $tagProperties) {
				$attributes = array();
				foreach ($tagProperties as $key => $value) {
					$attributes[] = $key . '="' . $value . '"';
				}
				$closing = isset($selfClosing[$tagName]) ? null : "</$tagName>";
				?>	<<?= $tagName ?> <?= implode(" ", $attributes) ?>><?= $closing ?>

<?php
			}
		}
	}

	protected function _addTag($tag, array $data = array()) {
		if (isset($this->_tags[$tag])) {
			$this->_tags[$tag][] = $data;
		} else {
			$this->_tags[$tag] = array($data);
		}
		return $this;
	}
}