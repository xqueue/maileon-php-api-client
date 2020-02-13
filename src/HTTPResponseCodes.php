<?php

namespace Maileon;

/**
 * This class allows translating between HTTP response codes and human readable strings
 *
 * @author Felix Heinrichs | Trusted Mails GmbH |
 * <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
abstract class HTTPResponseCodes
{
    /**
     * 200 OK
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2.1">HTTP/1.1 documentation</a>}.
     */
    const OK = 200;

    /**
     * 201 Created
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2.2">HTTP/1.1 documentation</a>}.
     */
    const CREATED = 201;

    /**
     * 202 Accepted
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2.3">HTTP/1.1 documentation</a>}.
     */
    const ACCEPTED = 202;

    /**
     * 204 No Content
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2.5">HTTP/1.1 documentation</a>}.
     */
    const NO_CONTENT = 204;

    /**
     * 301 Moved Permanently
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.2">HTTP/1.1 documentation</a>}.
     */
    const MOVED_PERMANENTLY = 301;

    /**
     * 303 See Other
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.4">HTTP/1.1 documentation</a>}.
     */
    const SEE_OTHER = 303;

    /**
     * 304 Not Modified
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.5">HTTP/1.1 documentation</a>}.
     */
    const NOT_MODIFIED = 304;

    /**
     * 307 Temporary Redirect
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.8">HTTP/1.1 documentation</a>}.
     */
    const TEMPORARY_REDIRECT = 307;

    /**
     * 400 Bad Request
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.1">HTTP/1.1 documentation</a>}.
     */
    const BAD_REQUEST = 400;

    /**
     * 401 Unauthorized
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.2">HTTP/1.1 documentation</a>}.
     */
    const UNAUTHORIZED = 401;

    /**
     * 403 Forbidden
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.4">HTTP/1.1 documentation</a>}.
     */
    const FORBIDDEN = 403;

    /**
     * 404 Not Found
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.5">HTTP/1.1 documentation</a>}.
     */
    const NOT_FOUND = 404;

    /**
     * 406 Not Acceptable
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.7">HTTP/1.1 documentation</a>}.
     */
    const NOT_ACCEPTABLE = 406;

    /**
     * 409 Conflict
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.10">HTTP/1.1 documentation</a>}.
     */
    const CONFLICT = 409;

    /**
     * 410 Gone
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.11">HTTP/1.1 documentation</a>}.
     */
    const GONE = 410;

    /**
     * 412 Precondition Failed
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.13">HTTP/1.1 documentation</a>}.
     */
    const PRECONDITION_FAILED = 412;

    /**
     * 415 Unsupported Media Type
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.16">HTTP/1.1 documentation</a>}.
     */
    const UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * 500 Internal Server Error
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.5.1">HTTP/1.1 documentation</a>}.
     */
    const INTERNAL_SERVER_ERROR = 500;

    /**
     * 503 Service Unavailable
     * see {@link <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.5.4">HTTP/1.1 documentation</a>}.
     */
    const SERVICE_UNAVAILABLE = 503;

    protected static $codes = array(
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        204 => "No Content",
        301 => "Moved Permanently",
        303 => "See Other",
        304 => "Not Modified",
        307 => "Temporary Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        403 => "Forbidden",
        404 => "Not Found",
        406 => "Not Acceptable",
        409 => "Conflict",
        410 => "Gone",
        412 => "Precondition Failed",
        415 => "Unsupported Media Type",
        500 => "Internal Server Error",
        503 => "Service Unavailable"
    );

    /**
     * Maps a numeric HTTP status code to the corresponding string message.
     *
     * @param number $httpStatusCode
     *  the HTTP status code to translate
     * @return string
     *  the corresponding string message, or an error message if the status code is unkown
     */
    public static function getStringFromHTTPStatusCode($httpStatusCode)
    {
        if (array_key_exists($httpStatusCode, HTTPResponseCodes::$codes) === true) {
            return HTTPResponseCodes::$codes[$httpStatusCode];
        } else {
            return "unknown error code: " . $httpStatusCode;
        }
    }
}
