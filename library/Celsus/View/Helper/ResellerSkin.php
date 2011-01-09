<?php
/**
 * View helper to add a reference to the reseller skin.
 *
 */
class Celsus_View_Helper_ResellerSkin {

	protected $_view;

	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
	}

	/**
	 * Inserts the reseller CSS class if necessary.
	 *
	 * @param string $type
	 * @return string
	 */
	public function resellerSkin() {
		if (Zend_Registry::isRegistered('reseller')) {
			$skin = 'css/' . Zend_Registry::get('reseller')->name . '.css';
			return '<link rel="stylesheet" href="' . $this->_view->versionedResource($skin) . '" type="text/css" />' . "\n";
		}
	}
}