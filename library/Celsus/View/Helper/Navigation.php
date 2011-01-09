<?php

/**
 * View helper add warnings for development environments.
 *
 */
class _Celsus_View_Helper_Navigation {
	
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
	public function navigation() {
		?>
		<!-- Start Menu -->
		<div id="menu_wrapper">
			<div id="menu">
				<?php
				if (BAM_Acl::currentUserIsAllowed('admin::objects')) {
					?>
					<!-- Admin Menu -->
					<ul>
						<li>
							<h2>Admin</h2>
							<ul>
								<li><a href="" class="x" onclick="return false;">Organise</a>
									<ul>
										<li><a href="/organise/subclients/" />Transfer Subclients</a></li>
									</ul>
								</li>												
							</ul>
						</li>
					</ul>
					<?
				}
				?>
			</div>
			<?= $this->_view->currentUserInfo(); ?>
		</div>
		<?php
	}
}
?>