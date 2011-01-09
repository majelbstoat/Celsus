<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Rest
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Route.php 53 2010-08-10 02:53:53Z jamie $
 */

/**
 * Rest routing
 *
 * @category Celsus
 * @package Celsus_Rest
 */
class Celsus_Rest_Route extends Zend_Rest_Route {

	public function match($request, $partial = false) {
		$return = parent::match($request, $partial = false);
		if ($return) {
			$this->_request->setControllerName($return[$this->_controllerKey]);
			$this->controller = $this->_dispatcher->getControllerClass($this->_request);
			$this->_dispatcher->loadClass($this->controller);
			$this->action = $return[$this->_actionKey] . "Action";
			if (!method_exists($this->controller, $this->action)) {
				$this->_request->setControllerName("");
				$return = false;
			}
		}
		return $return;
	}

    /**
     * Sub-classed class returning a Celsus_Rest_Route instead of a Zend_Rest_Route.
     */
    public static function getInstance(Zend_Config $config)
    {
        $frontController = Zend_Controller_Front::getInstance();
        $defaultsArray = array();
        $restfulConfigArray = array();
        foreach ($config as $key => $values) {
        	if ($key == 'type') {
        		// do nothing
        	} elseif ($key == 'defaults') {
        		$defaultsArray = $values->toArray();
        	} else {
        		$restfulConfigArray[$key] = explode(',', $values);
        	}
        }
        $instance = new static($frontController, $defaultsArray, $restfulConfigArray);
        return $instance;
    }

}