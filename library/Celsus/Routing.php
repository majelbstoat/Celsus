<?php

class Celsus_Routing {

	protected static $_routes = array();

	protected static $_instance = null;

	/**
	 * Loads routes from config.
	 *
	 * @todo Aggressive caching here.
	 */
	public static function setRoutes(Zend_Config $routes)
	{
		self::$_routes = $routes;
	}

	public static function getRoutes() {
		return self::$_routes;
	}

	public static function getRoute($routeName) {
		return self::$_routes->$routeName;
	}

	public static function linkTo($routeName, $params = array()) {
		$routeDefinition = self::$_routes->$routeName;
		$route = $routeDefinition->route;

		foreach ($params as $name => $value) {
			$route = str_replace(":$name", $value, $route);
		}

		return $route;
	}
}