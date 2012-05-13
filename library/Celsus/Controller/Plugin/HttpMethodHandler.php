<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2012 Jamie Talbot (http://jamietalbot.com)
 */

/**
 * Handles HTTP DELETE requests
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Plugin_HttpMethodHandler extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($request->isDelete() || $request->isPut() || $request->isPost()) {
            $params = array();
            parse_str($request->getRawBody(), $params);
            $request->setParams($params);
        } elseif ($request->isGet()) {
        	$request->setParams($request->getQuery());
        }
    }
}
