<?php

class Celsus_Controller_Plugin_ErrorHandler extends Zend_Controller_Plugin_Abstract
{
    /**
     * Flag; are we already inside the error handler loop?
     * @var bool
     */
    protected $_isInsideErrorHandlerLoop = false;

    /**
     * Exception count logged at first invocation of plugin
     * @var int
     */
    protected $_exceptionCountAtFirstEncounter = 0;

    /**
     * Constructor
     *
     * Options may include:
     * - module
     * - controller
     * - action
     *
     * @param  Array $options
     * @return void
     */
    public function __construct(Array $options = array()) {}

    /**
     * Route shutdown hook -- Ccheck for router exceptions
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $this->_handleError($request);
    }

    /**
     * Pre dispatch hook -- check for exceptions and dispatch error handler if
     * necessary
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_handleError($request);
    }

    /**
     * Post dispatch hook -- check for exceptions and dispatch error handler if
     * necessary
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_handleError($request);
    }

    /**
     * Handle errors and exceptions
     *
     * If the 'noErrorHandler' front controller flag has been set,
     * returns early.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    protected function _handleError(Zend_Controller_Request_Abstract $request)
    {
        $frontController = Zend_Controller_Front::getInstance();

        $response = $this->getResponse();

        if ($this->_isInsideErrorHandlerLoop) {
            $exceptions = $response->getException();
            if (count($exceptions) > $this->_exceptionCountAtFirstEncounter) {
                // Very bad news.  Exception thrown by error handler; tell the front controller to throw it
                $frontController->throwExceptions(true);
                throw array_pop($exceptions);
            }
        }

        // check for an exception AND allow the error handler controller the option to forward
        if (($response->isException()) && (!$this->_isInsideErrorHandlerLoop)) {
            $this->_isInsideErrorHandlerLoop = true;

            // Get exception information
            $error            = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            $exceptions       = $response->getException();
            $exception        = $exceptions[0];
            $exceptionType    = get_class($exception);
            $error->exception = $exception;

            // @todo Maybe remove this.
            switch ($exceptionType) {
                default:
                    $error->type = $exception->getCode();
                    break;
            }

            // Keep a copy of the original request
            $error->request = clone $request;

            // get a count of the number of exceptions encountered
            $this->_exceptionCountAtFirstEncounter = count($exceptions);

            // Forward to the error handler
            $request->setError($error)
                    ->setControllerName('error')
                    ->setActionName('error')
                    ->setDispatched(false);
        }
    }
}
