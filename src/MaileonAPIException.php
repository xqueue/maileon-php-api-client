<?php

namespace de\xqueue\maileon\api\client;

use Exception;
use RuntimeException;

/**
 * An exception that is thrown when a technical error has occurred either in the communication
 * with Maileon's REST API or in the API itself.
 */
class MaileonAPIException extends RuntimeException
{
    /** @var false|string */
    private $response;

    /**
     * @param Exception|null $previous
     */
    public function __construct(
        $message = '',
        $response = false,
        $code = 0,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * @return false|string The HTTP response body if there was one, false otherwise. If a CURL error occurred,
     * this returns the ID of the CURL exception, see e.g. https://curl.se/libcurl/c/libcurl-errors.html
     */
    public function getResponse()
    {
        return $this->response;
    }
}
