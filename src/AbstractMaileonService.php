<?php

namespace de\xqueue\maileon\api\client;

use CurlHandle;
use de\xqueue\maileon\api\client\contacts\PreferenceCategory;
use RuntimeException;

/**
 * Abstract base class for all the service accessing individual resources. This class handles
 * the basic authentication and provides convenience methods to access the four HTTP methods
 * used in RESTful web services.
 *
 * @author Felix Heinrichs
 * @author Marcus Beckerle
 */
abstract class AbstractMaileonService
{

    /**
     * Mime type for the maileon xml data format.
     *
     * @var string $MAILEON_XML_MIME_TYPE
     */
    public static $MAILEON_XML_MIME_TYPE = 'application/vnd.maileon.api+xml';

    /**
     * Configuration object storing BASE_URI and API_KEY to access the maileon REST API.
     *
     * @var array $configuration
     */
    protected $configuration;

    /**
     * Base64 encoded version of the API key.
     *
     * @var string $encodedApiKey
     */
    protected $encodedApiKey;

    /**
     * If true, print debug output (e.g. sent header will be printed)
     *
     * @var boolean $debug
     */
    protected $debug = false;

    /**
     * If true, throw an exception if a transmission or server error occures
     *
     * @var boolean $throwException
     */
    protected $throwException = true;

    /**
     * If a proxy is used, provide the proxy IP here
     *
     * @var string $proxy_host
     */
    protected $proxy_host;

    /**
     * If a proxy is used, use this port. Default is 80.
     *
     * @var int $proxy_port
     */
    protected $proxy_port = 80;

    /**
     * If set, this sets the timeout for a CURL request
     *
     * @var int $timeout
     */
    protected $timeout;

    private $verboseOut = null;

    /**
     * Creates a new instance of the service.
     *
     * @param string[] $config
     *  the API call configuration array
     * @throws RuntimeException if API key is not set
     */
    public function __construct(array $config)
    {
        // check for valid configuration object
        if (!array_key_exists('API_KEY', $config)) {
            throw new RuntimeException('API_KEY not set');
        }
        if (!array_key_exists('BASE_URI', $config)) {
            $config['BASE_URI'] = "https://api.maileon.com/1.0";
        }
        if (array_key_exists('THROW_EXCEPTION', $config)) {
            $this->throwException = $config['THROW_EXCEPTION'];
        }
        if (array_key_exists('DEBUG', $config)) {
            $this->debug = $config['DEBUG'];
        }

        // Proxy config
        if (array_key_exists('PROXY_HOST', $config)) {
            $this->proxy_host = $config['PROXY_HOST'];
        }
        if (array_key_exists('PROXY_PORT', $config)) {
            $this->proxy_port = $config['PROXY_PORT'];
        }

        // Timeout in seconds
        if (array_key_exists('TIMEOUT', $config)) {
            $this->timeout = $config['TIMEOUT'];
        }
        $this->configuration = $config;
        $this->encodedApiKey = base64_encode($config['API_KEY']);
    }

    /**
     * (De)activates printing debug output.
     * <strong>CAUTION:</strong> enabling this in production may compromise sensitive information.
     *
     * @param bool $isDebug
     *  true to enable debugging, false to disable it
     */
    public function setDebug($isDebug)
    {
        $this->debug = $isDebug;
    }

    /**
     * @return boolean
     *  true if debug output is enabled, false otherwise
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Performs a GET operation on a resource
     *
     * @param string $resourcePath
     *  the path of the resource to GET
     * @param string[] $queryParameters
     *  any additional query parameters
     * @param string $mimeType
     *  the acceptable response MIME type
     * @param mixed $deserializationType
     *  The name of the class this result should be deserialized as. Use
     *      array( 'array', 'typename' ) to deserialize arrays of a type.
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function get(
        $resourcePath,
        $queryParameters = array(),
        $mimeType = 'application/vnd.maileon.api+xml',
        $deserializationType = null
    ) {
        $curlSession = $this->prepareSession($resourcePath, $queryParameters, $mimeType);
        return $this->performRequest($curlSession, $deserializationType);
    }

    /**
     * Performs a PUT operation (i.e. an update) on a resource.
     *
     * @param string $resourcePath
     *  the path of the resource to PUT
     * @param string $payload
     *  the payload data to PUT, i.e. the data to update the current state of the resource with
     * @param array $queryParameters
     *  any additional query parameters
     * @param string $mimeType
     *  the acceptable response MIME type
     * @param mixed $deserializationType
     *  The name of the class this result should be deserialized as. Use
     *      array( 'array', 'typename' ) to deserialize arrays of a type.
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function put(
        $resourcePath,
        $payload = "",
        $queryParameters = array(),
        $mimeType = 'application/vnd.maileon.api+xml',
        $deserializationType = null
    ) {
        $curlSession = $this->prepareSession($resourcePath, $queryParameters, $mimeType);

        /*
         * PUT does not work as expected when passing post data, see
         * http://developers.sugarcrm.com/wordpress/2011/11/22/howto-do-put-requests-with-php-curl-without-writing-to-a-file/
         * Because of this, we use a custom request here.
         */
        curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $payload);
        return $this->performRequest($curlSession, $deserializationType);
    }

    /**
     * Performs a POST operation (i.e. creates a new instance) on a resource.
     *
     * @param string $resourcePath
     *  the path of the resource to POST. This is typically the parent (or owner) resource
     *  of the resource instance to create.
     * @param string $payload
     *  the data to POST, i.e. the contents of the new resource instance
     * @param array $queryParameters
     *  any additional query parameters
     * @param string $mimeType
     *  the acceptable response MIME type
     * @param mixed $deserializationType
     *  The name of the class this result should be deserialized as. Use
     *      array( 'array', 'typename' ) to deserialize arrays of a type.
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function post(
        $resourcePath,
        $payload = "",
        $queryParameters = array(),
        $mimeType = 'application/vnd.maileon.api+xml',
        $deserializationType = null,
        $contentType = null,
        $contentLength = null
    ) {
        $curlSession = $this->prepareSession($resourcePath, $queryParameters, $mimeType, $contentType, $contentLength);
        curl_setopt($curlSession, CURLOPT_POST, true);
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $payload);
        return $this->performRequest($curlSession, $deserializationType);
    }

    /**
     * Performs a DELETE operation on a resource.
     *
     * @param string $resourcePath
     *  the resource to DELETE
     * @param array $queryParameters
     *  any additional query parameters
     * @param string $mimeType
     *  the acceptable response MIME type
     * @param mixed $deserializationType
     *  The name of the class this result should be deserialized as. Use
     *      array( 'array', 'typename' ) to deserialize arrays of a type.
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function delete(
        $resourcePath,
        $queryParameters = array(),
        $mimeType = 'application/vnd.maileon.api+xml',
        $deserializationType = null
    ) {
        $curlSession = $this->prepareSession($resourcePath, $queryParameters, $mimeType);
        curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "DELETE");
        return $this->performRequest($curlSession, $deserializationType);
    }

    /**
     * @return false|CurlHandle
     */
    private function prepareSession(
        $resourcePath,
        $queryParameters,
        $mimeType,
        $contentType = null,
        $contentLength = null
    ) {
        $requestUrl = $this->constructRequestUrl($resourcePath, $queryParameters);
        $headers = $this->constructHeaders($mimeType, $contentType, $contentLength);
        $curlSession = curl_init($requestUrl);
        $options = array(
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FAILONERROR => false,
            CURLOPT_VERBOSE => $this->debug
        );

        if ($this->debug) {
            $this->verboseOut = fopen("php://temp", "rw+");
            $options[CURLOPT_STDERR] = $this->verboseOut;
        }

        if ($this->timeout) {
            $options[CURLOPT_CONNECTTIMEOUT] = $this->timeout;
            $options[CURLOPT_TIMEOUT] = $this->timeout;
        }

        if ($this->proxy_host) {
            $options[CURLOPT_PROXY] = $this->proxy_host;
            $options[CURLOPT_PROXYPORT] = $this->proxy_port;
        }

        curl_setopt_array($curlSession, $options);
        return $curlSession;
    }

    private function constructRequestUrl($resourcePath, $queryParameters)
    {
        $requestUrl = $this->configuration['BASE_URI'] . "/" . $resourcePath;

        if (isset($queryParameters) && !empty($queryParameters)) {
            $requestUrl = $requestUrl . '?';

            foreach ($queryParameters as $key => $value) {
                // If the query parameter is an array, then walk through the array, create a multi-
                // valued query string and replace boolean fields with it's string counterpart.

                // Such query parameters usually won't ever occur since Maileon doesn't support them.
                // Example query: ?emails[]=foo@bar.baz&emails[]=alice@bob.eve&emails[]=a@b.xy

                // Multivalued parameters are being handled in Maileon by sending duplicate parameters.
                // Example query: ?emails=foo@bar.baz&emails=alice@bob.eve&emails=a@b.xy

                // Furthermore the API client's functions and their parameters haven't been designed to
                // support or use query parameters with type array.
                if (is_array($value)) {
                    foreach ($value as $innerValue) {
                        if ($innerValue === true) {
                            $requestUrl .= $key . '=true&';
                        } elseif ($innerValue === false) {
                            $requestUrl .= $key . '=false&';
                        } else {
                            $requestUrl .= $key . '=' . $innerValue . '&';
                        }
                    }
                }
                
                // Handle non array query parameters
                else {
                    if ($value === true) {
                        $requestUrl .= $key . '=true&';
                    } elseif ($value === false) {
                        $requestUrl .= $key . '=false&';
                    } else {
                        $requestUrl .= $key . '=' . $value . '&';
                    }
                }
            }

            $requestUrl = rtrim($requestUrl, '&');
        }

        return $requestUrl;
    }

    private function constructHeaders($mimeType, $contentType = null, $contentLength = null)
    {
        $headers = array(
            "Content-type: " . ($contentType === null ? $mimeType : $contentType),
            "Accept: " . $mimeType,
            "Authorization: Basic " . $this->encodedApiKey,
            "Expect:"
        );
        
        if ($contentLength !== null) {
            $headers []= "Content-Length: " . $contentLength;
        }
        
        return $headers;
    }

    /**
     * Perform the currently initialized request
     *
     * @param CurlHandle $curlSession
     *  the curl session
     * @param mixed $deserializationType
     *  The name of the class this result should be deserialized as. Use
     *      array( 'array', 'typename' ) to deserialize arrays of a type.
     * @return MaileonAPIResult
     * @throws MaileonAPIException
     */
    private function performRequest($curlSession, $deserializationType = null)
    {
        $response = curl_exec($curlSession);
        // coerce all false values to null
        $response = $response ? $response : null;
        try {
            $result = new MaileonAPIResult($response, $curlSession, $this->throwException, $deserializationType);
            $this->printDebugInformation($curlSession, $result);
            curl_close($curlSession);
            return $result;
        } catch (MaileonAPIException $e) {
            if ($this->debug) {
                $this->printDebugInformation($curlSession, null, $this->throwException ? null : $e);
            }
            curl_close($curlSession);
            if ($this->throwException) {
                throw $e;
            }
            return null;
        }
    }

    protected function appendArrayFields($params, $name, $fieldValues)
    {
        if (isset($fieldValues) && is_array($fieldValues) && count($fieldValues) > 0) {
            $params ["$name"] = array();
            foreach ($fieldValues as $value) {
                if ($value === true) {
                    $params ["$name"] [] = "true";
                } else if ($value === false) {
                    $params ["$name"] [] = "false";
                } else if ($value instanceof PreferenceCategory) {
                    $params[$name] = urlencode((string)$value->name);
                } else {
                    $params ["$name"] [] = urlencode($value);
                }
            }
        }
        return $params;
    }

    private function printDebugInformation($curlSession, $result = null, $exception = null)
    {
        if ($this->debug) {
            rewind($this->verboseOut);
            $sessionLog = stream_get_contents($this->verboseOut);
            $sessionLog = preg_replace("/^Authorization: .*$/m", "Authorization: ***redacted***", $sessionLog);
            if (defined('RUNNING_IN_PHPUNIT') && RUNNING_IN_PHPUNIT) {
                echo "\n";
                echo "cURL session log:\n";
                echo $sessionLog . "\n";
                if ($result != null) {
                    echo "Result:\n";
                    echo $result->toString() . "\n";
                }
                if ($exception != null) {
                    echo "Caught exception:\n";
                    echo $exception . "\n";
                }
                if (curl_errno($curlSession)) {
                    echo "cURL Error: \n";
                    echo htmlentities(curl_error($curlSession));
                }
            } else {
                echo "<h3>cURL session log</h3>\n";
                echo "<pre>\n";
                echo htmlentities($sessionLog);
                echo "</pre>\n";
                if ($result != null) {
                    echo "<h3>Result</h3>\n";
                    echo "<pre>\n";
                    echo htmlentities($result->toString());
                    echo "</pre>\n";
                }
                if ($exception != null) {
                    echo "<h3>Exception</h3>\n";
                    echo "<pre>\n";
                    echo htmlentities($exception);
                    echo "</pre>\n";
                }
                if (curl_errno($curlSession)) {
                    echo "<h3>cURL Error</h3>\n";
                    echo "<pre>\n";
                    echo 'Curl error: ' . htmlentities(curl_error($curlSession));
                    echo "</pre>\n";
                }
            }
            $this->verboseOut = null;
        }
    }
}
