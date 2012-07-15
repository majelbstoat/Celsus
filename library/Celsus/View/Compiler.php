<?php

class Celsus_View_Compiler {

	public function compile($section) {

		$stream = 'data://text/plain,' . urlencode($section);

		ob_start();
		include $stream;
		return ob_get_clean();
	}

	protected function _assets($type) {
		?><h1>This is an action of type <?= $type ?></h1><?php
	}

}