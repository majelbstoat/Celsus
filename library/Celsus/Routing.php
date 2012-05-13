<?php

class Celsus_Routing {

	const SYNTAX_DELIMITER = '/';
	const SYNTAX_NAME_PREFIX = ':';
	const SYNTAX_WILDCARD = '*';
	const SYNTAX_ACTION_KEY = '__action';

	protected static $_routes = array();

	protected static $_routeMap = array();

	/**
	 * Loads routes from config.
	 *
	 * @todo Aggressive caching here.
	 */
	public static function setRoutes(Zend_Config $routes)
	{
		self::$_routes = $routes;
	}

	/**
	 * Gets a route map that is optimised for matching by path.
	 *
	 * auth/token/:identifier
	 * auth/logout
	 *
	 * would generate an array structure like so:
	 *
	 * auth
	 *   => token
	 *     => :identifier
	 *   => logout
	 *
	 * @todo Cache this aggressively.
	 *
	 * @return array
	 */
	public static function getRouteMap() {
		if (!self::$_routeMap) {
			$routeMap = array();

			// Iterate each route.
			foreach (self::$_routes as $routeName => $routeDefinition) {
				$routeComponents = explode(self::SYNTAX_DELIMITER, $routeDefinition->route);
				$routePointer = & $routeMap;

				// Iterate through the route components, creating a child entry for each.
				while ($routeComponents) {
					$routeComponent = array_shift($routeComponents);

					if (self::SYNTAX_NAME_PREFIX == substr($routeComponent, 0, 1)) {
						$routeComponent = self::SYNTAX_WILDCARD;
					}

					// If there are no more route components, this is the leaf, and we can store our route name for ease of use.
					$child = $routeComponents ? array() : array('__action' => $routeName);
					if (!isset($routePointer[$routeComponent])) {
						$routePointer[$routeComponent] = $child;
					} else {
						$routePointer[$routeComponent] += $child;
					}

					// If the child is empty, it must not be the route name, so move the pointer further down the tree.
					$routePointer = & $routePointer[$routeComponent];
				}
			}
			self::$_routeMap = $routeMap;

			// This does not remove the entry from the route map, just the reference, which could be troublesome.
			unset($routePointer);
		}

		return self::$_routeMap;
	}

	public static function sanitisePath($path) {
		$path = trim($path, self::SYNTAX_DELIMITER);

		if (false !== strpos($path, self::SYNTAX_ACTION_KEY)) {
			throw new Celsus_Exception("Unsafe attempt to route __action", Celsus_Http::NOT_FOUND);
		} elseif (false !== strpos($path, self::SYNTAX_WILDCARD)) {
			throw new Celsus_Exception("Unsafe attempt to route *", Celsus_Http::NOT_FOUND);
		}

		return $path;
	}

	public static function getRoutes() {
		return self::$_routes;
	}

	public static function getRouteNameByPath($path) {
		$path = self::sanitisePath($path);
		$routePointer = self::getRouteMap();

		$pathComponents = explode(self::SYNTAX_DELIMITER, $path);

		foreach ($pathComponents as $pathComponent) {

			if (isset($routePointer[$pathComponent])) {
				$routePointer = & $routePointer[$pathComponent];
			} elseif (isset($routePointer[self::SYNTAX_WILDCARD])) {
				$routePointer = & $routePointer[self::SYNTAX_WILDCARD];
			} else {
				return null;
			}
		}

		// If we got here, we matched all the way to the end of the path.
		// But, we still need to check the route ends there as well.
		return isset($routePointer[self::SYNTAX_ACTION_KEY]) ? $routePointer[self::SYNTAX_ACTION_KEY] : null;
	}

	public static function getRouteByPath($path) {
		return self::getRouteByName(self::getRouteNameByPath($path));
	}

	public static function getRouteByName($routeName) {
		return self::$_routes->$routeName;
	}

	public static function extractRouteParametersFromPath($routeDefinition, $path) {
		$params = array();

		$routeComponents = explode(self::SYNTAX_DELIMITER, trim($routeDefinition->route));
		$pathComponents = explode(self::SYNTAX_DELIMITER, $path);

		$count = count($routeComponents);

		for ($i = 0; $i < $count; $i++) {
			if (self::SYNTAX_NAME_PREFIX == substr($routeComponents[$i], 0, 1)) {
				$params[substr($routeComponents[$i], 1)] = $pathComponents[$i];
			}
		}
		return $params;
	}

	public static function linkTo($routeName, $params = array()) {
		$routeDefinition = self::$_routes->$routeName;
		$route = $routeDefinition->route;

		foreach ($params as $name => $value) {
			$route = str_replace(":$name", $value, $route);
		}

		return $route;
	}

	public static function absoluteLinkTo($routeName, $params = array()) {
		return Celsus_Application::rootUrl() . self::SYNTAX_DELIMITER . self::linkTo($routeName, $params);

	}

	/**
	 * Clears the stored routes.  Primary used for testing.
	 */
	public static function clearRoutes() {
		self::$_routeMap = null;
		self::$_routes = null;
	}
}