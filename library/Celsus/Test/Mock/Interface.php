<?php

interface Celsus_Test_Mock_Interface {

	/**
	 * Takes care of mocking an object and setting its use in the application.
	 *
	 * @return Celsus_Test_Mock_Broker
	 *
	 */
	public function mock();
}