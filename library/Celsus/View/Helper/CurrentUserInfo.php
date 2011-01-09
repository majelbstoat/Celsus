<?php

/**
 * View helper add warnings for development environments.
 *
 */
class Celsus_View_Helper_CurrentUserInfo {
	
	protected $_view;

	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
	}

	/**
	 * Displays the name of the current user, and a logout button.
	 *
	 * @param string $type
	 * @return string
	 */
	public function currentUserInfo() {
		?>				
		<div id="current_user_info">
			Logged in as: <a href="/settings/" title="Change Preferences"><?= Zend_Auth::getInstance()->getIdentity()->realname ?></a> | <a href="/logout/">Logout</a>
		</div>
		<?php
	}
}
?>