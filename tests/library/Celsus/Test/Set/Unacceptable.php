<?php

class Celsus_Test_Set_Unacceptable implements Celsus_Test_Set_Interface {

	/**
	 * Mock object always returns false, because it's always unacceptable.
	 *
	 * @return boolean
	 */
	public function acceptable() {
		return false;
	}

}