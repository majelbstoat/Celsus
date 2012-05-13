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
            ),
        	'auth_tokens' => array(
        		"route" => "auth/token",
        		"controller" => "auth",
        		"methods" => array(
        			"post" => array(
        				"action" => "create",
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
        $this->assertEquals("home", $routeName, "Empty route not matched");
    }

    public function testIncompletePathShouldNotMatch() {
    	$path = "auth";
        $routeName = Celsus_Routing::getRouteNameByPath($path);
        $this->assertNull($routeName, "/auth route should not have matched");
    }

	public function testPartialRoutesShouldBeHandled() {
    	$path = "auth/token";
        $routeName = Celsus_Routing::getRouteNameByPath($path);
        $this->assertEquals("auth_tokens", $routeName, "/auth/token route not found");
	}

	public function testParametersShouldBeExtractedFromTheRoute() {
    	$path = "auth/token/42";
        $routeDefinition = Celsus_Routing::getRouteByPath($path);
        $parameters = Celsus_Routing::extractRouteParametersFromPath($routeDefinition, $path);
        $expected = array(
        	'identifier' => 42
        );
        $this->assertEquals($expected, $parameters, "Identifier not extracted");
	}
}