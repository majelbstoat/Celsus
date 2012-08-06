<?php

require_once 'Celsus/Controller/Response/Http.php';

class Celsus_Controller_Response_HttpTestCase extends Celsus_Controller_Response_Http {
    /**
     * "send" headers by returning array of all headers that would be sent
     *
     * @return array
     */
    public function sendHeaders()
    {
        $headers = array();
        foreach ($this->_headersRaw as $header) {
            $headers[] = $header;
        }
        foreach ($this->_headers as $header) {
            $name = $header['name'];
            $key  = strtolower($name);
            if (array_key_exists($name, $headers)) {
                if ($header['replace']) {
                    $headers[$key] = $header['name'] . ': ' . $header['value'];
                }
            } else {
                $headers[$key] = $header['name'] . ': ' . $header['value'];
            }
        }
        return $headers;
    }

    /**
     * Can we send headers?
     *
     * @param  bool $throw
     * @return void
     */
    public function canSendHeaders($throw = false)
    {
        return true;
    }

    /**
     * Return the concatenated body segments
     *
     * @return string
     */
    public function outputBody()
    {
        $fullContent = '';
        foreach ($this->_body as $content) {
            $fullContent .= $content;
        }
        return $fullContent;
    }

    /**
     * Get body and/or body segments
     *
     * @param  bool|string $spec
     * @return string|array|null
     */
    public function getBody($spec = false)
    {
        if (false === $spec) {
            return $this->outputBody();
        } elseif (true === $spec) {
            return $this->_body;
        } elseif (is_string($spec) && isset($this->_body[$spec])) {
            return $this->_body[$spec];
        }

        return null;
    }

    /**
     * "send" Response
     *
     * Concats all response headers, and then final body (separated by two
     * newlines)
     *
     * @return string
     */
    public function sendResponse()
    {
        $headers = $this->sendHeaders();
        $content = implode("\n", $headers) . "\n\n";

        if ($this->isException() && $this->renderExceptions()) {
            $exceptions = '';
            foreach ($this->getException() as $e) {
                $exceptions .= $e->__toString() . "\n";
            }
            $content .= $exceptions;
        } else {
            $content .= $this->outputBody();
        }

        return $content;
    }
}
