<?php

class Celsus_RoutingTest extends PHPUnit_Framework_TestCase {

	protected $_sampleData = null;

	protected $_object = null;

	public function setUp() {
        $this->_sampleData = new Zend_Config(array(
            'home' => array(
                "route" => "",
                "controller" => "index",
                "methods" => array(
                    "get" => array(
                        "action" => "index",
                        "contexts" => array(
                            "application" => array(
                                "login" => true
                            )
                        )
                    )
                )
            ),
            'auth_token' => array(
                "route" => "auth/token/:identifier",
                "controller" => "auth",
                "methods" => array(
                    "get" => array(
                        "action" => "token",
                        "parameters" => array(
                            "identifier"
                        ),
                        "contexts" => array(
                            "api" => array()
                        )
                    )
                )
            )
        ));

        Celsus_Routing::setRoutes($this->_sampleData);
    }

    public function tearDown() {
        Celsus_Routing::clearRoutes();
    }

    public function testEmptyPathShouldMatchEmptyRoute() {
        $path = "";
        $routeName = Celsus_Routing::getRouteNameByPath($path);
        $this->assertEquals("home", $routeName);
    }

    public function testIncompletePathShouldNotMatch() {
        $path = "auth/token";
        $routeName = Celsus_Routing::getRouteNameByPath($path);
        $this->assertNull($routeName);
    }
}
