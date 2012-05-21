<?php

class Celsus_Context_Resolver_Default implements Celsus_Context_Resolver_Interface {

	public static function resolve(Celsus_Controller_Request_Http $request) {
		return true;
	}


}