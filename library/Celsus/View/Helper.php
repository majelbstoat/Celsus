<?php

abstract class Celsus_View_Helper {

	public function __toString() {
		ob_start();
		$this->render();
		return ob_get_clean();
	}
}