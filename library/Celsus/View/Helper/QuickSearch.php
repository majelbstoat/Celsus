<?php

/**
 * View helper add warnings for development environments.
 *
 */
class Celsus_View_Helper_QuickSearch {
	
	protected $_view;

	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
	}

	/**
	 * Displays the quick search box.
	 *
	 * @param string $type
	 * @return string
	 */
	public function quickSearch() {
		?>				
		<div id="quick_search_box">
		<form name="search" action="/main.php" method="post">
			<img src="<?= $this->_view->versionedResource('i/search.gif') ?>" />
			<input type="hidden" name="action" value="search" />
			<input type="text" id="search_box" name="key" value="Search..." size="15" onclick="if ('Search...' == this.value) this.value = '';" />
			<select name="searchtype">
			<?php
			foreach (BAM_Search_Quick::getSearchModes() as $mode => $title) {
				?><option value="<?= $mode ?>"><?= $title ?></option><?php
			}
			?>	
			</select>
			<button type="submit" id="search_button">Go</button>
		</form>
		</div>
		<?php
	}
}
?>