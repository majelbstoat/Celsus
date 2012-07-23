<?php

class Celsus_OpenGraph {

	const PREFIX_OPENGRAPH = 'og';
	const PREFIX_FACEBOOK = 'fb';

	protected static $_namespaces = array(
		self::PREFIX_OPENGRAPH => 'http://ogp.me/ns',
		self::PREFIX_FACEBOOK => 'http://ogp.me/ns/fb'
	);

	public static function registerNamespace($prefix, $definitionUrl) {
		self::$_namespaces[$prefix] = $definitionUrl;
	}

	public static function getNamespaces(array $prefixes = array()) {
		return array_diff_key(self::$_namespaces, array_flip($prefixes));
	}


}