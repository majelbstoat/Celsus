<?php

/**
 * View helper add warnings for development environments.
 *
 */
class Celsus_View_Helper_StagingWarning {
	
	protected $_view;

	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
	}

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