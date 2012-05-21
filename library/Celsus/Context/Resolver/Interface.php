<?php

interface Celsus_Context_Resolver_Interface {

	/**
	 * Determines whether this context is applicable for the given request.
	 *
	 * Returns true if this context matches, false otherwise.
	 *
	 * @param Celsus_Controller_Request_Http $request
	 * @return boolean
	 */
	public static function resolve(Celsus_Controller_Request_Http $request);
}