<?php

class Celsus_Context_Resolver_Facebook implements Celsus_Context_Resolver_Interface {

	public static function resolve(Celsus_Controller_Request_Http $request) {

		// @todo Check for facebookexternalhit/1.1 in the User Agent.
		return false;
	}


}