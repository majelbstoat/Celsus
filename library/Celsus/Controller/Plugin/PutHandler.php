<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: PutHandler.php 49 2010-07-18 23:23:29Z jamie $
 */

/**
 * Handles HTTP PUT requests.
 *
 * @category Celsus
 * @package Celsus_Controller
 */
class Celsus_Controller_Plugin_PutHandler extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($this->_request->isPut()){
            $putParams = array();
            parse_str($this->_request->getRawBody(), $putParams);
            $request->setParams($putParams);
        }
    }
}