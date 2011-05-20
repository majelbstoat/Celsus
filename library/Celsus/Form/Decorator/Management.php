<?php

class Celsus_Form_Decorator_Management extends Zend_Form_Decorator_Abstract {

	/**
	 * Processes the sidebar of a management form.
	 *
	 * @return string
	 * @todo Add the search panel.
	 */
	protected function _processSidebar() {
		$return = '';
		$sidebarForms = $this->getElement()->getSidebarForms();
		foreach ($sidebarForms as $sidebarForm) {
			$return .= $sidebarForm->render();
		}
		return $return;
	}
	public function render($content) {
		$sidebar = $this->_processSidebar();
		return '<div class="management">' .
			$content . $sidebar .
			'</div>';
	}
}
?>