<?php

class Celsus_Context_Resolver_Json implements Celsus_Context_Resolver_Interface {

	const HEADER_ACCEPT_TYPE = 'application/json';
	const HEADER_CELSUS_FORMAT = 'json';

	public static function resolve(Celsus_Controller_Request_Http $request) {

		return (false !== strpos($request->getHeader('Accept'), self::HEADER_ACCEPT_TYPE) || self::HEADER_CELSUS_FORMAT === $request->getHeader('X-Celsus-Format'));

	}


}