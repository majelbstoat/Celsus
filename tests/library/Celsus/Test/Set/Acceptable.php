<?php

class Celsus_Test_Set_Acceptable implements Celsus_Test_Set_Interface {

	/**
	 * Mock object always returns true, because it's always acceptable.
	 *
	 * @return boolean
	 */
	public function acceptable() {
		return true;
	}

}