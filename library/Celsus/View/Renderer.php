<?php

abstract class Celsus_View_Renderer {

	protected function _helper($name) {
		return Celsus_View_Helper_Broker::getHelper($name);
	}
}