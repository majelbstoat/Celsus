<?php

class Celsus_Context_Resolver_Json implements Celsus_Context_Resolver_Interface {

	const HEADER_CONTENT_TYPE = 'application/json';
	const HEADER_CELSUS_FORMAT = 'json';

	public static function resolve(Celsus_Controller_Request_Http $request) {

		return (self::HEADER_CONTENT_TYPE == $request->getHeader('Content-Type') || self::HEADER_CELSUS_FORMAT == $request->getHeader('X-Celsus-Format'))		;

	}


}