<?php

namespace de\xqueue\maileon\api\client;

use de\xqueue\maileon\api\client\json\JSONDeserializer;
use de\xqueue\maileon\api\client\xml\XMLDeserializer;

/**
 * The result of making a call to the Maileon REST API.
 *
 * This class encapsulates the technical details of the REST API's HTTP response. In order
 * to retrieve the payload result in deserialized form, just call
 * com_maileon_api_MaileonAPIResult::getResult().
 *
 * However, this class also allows the underlying HTTP response information to be queried,
 * including the returned status code (com_maileon_api_MaileonAPIResult::getStatusCode())
 * and content type (com_maileon_api_MaileonAPIResult::getContentType()) as well as the raw
 * HTTP response body data (com_maileon_api_MaileonAPIResult::getBodyData()).
 */
class MaileonAPIResult
{
    private $curlSession;

    private $statusCode;
    private $contentType;

    private $bodyData = null;
    private $responseHeaders = array();
    private $resultXML = null;
    private $result = null;

    private $deserializationType = null;

    /**
     * Creates a new result object from the curl response and session data.
     *
     * @param string $response
     *  the HTTP response data, null if there was none
     * @param mixed $curlSession
     *  the cURL session that was used
     * @param bool $throwException
     *  if true, an exception will be thrown in case of a connection or server error
     * @param mixed $deserializationType
     *  The name of the class this result should be deserialized as. Use
     *      array( 'array', 'typename' ) to deserialize arrays of a type.
     * @throws MaileonAPIException
     *  if $throwException == true and there was a connection problem or a server error occurred
     */
    public function __construct($response, $curlSession, $throwException = true, $deserializationType = null)
    {
            $this->bodyData = $this->getBodyFromCurlResponse($response);
            $this->curlSession = $curlSession;
            $this->deserializationType = $deserializationType;
            $this->responseHeaders = $this->getHeaderArrayFromCurlResponse($response);
            $this->checkResult($throwException);
    }

    // See https://stackoverflow.com/questions/10589889/returning-header-as-array-using-curl
    private function getHeaderArrayFromCurlResponse($response)
    {
        $headers = array();

        $start = 0;

        // Check if there is a proxy
        if (strpos($response, "\r\n\r\n") != strrpos($response, "\r\n\r\n")) {
            $start = strpos($response, "\r\n\r\n")+4;
        }
        $header_text = substr($response, $start, strrpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                if (strpos($line, ':') != 0) {
                    list ($key, $value) = explode(': ', $line);
                    $headers[$key] = $value;
                }
            }
        }

        return $headers;
    }
    
    private function getBodyFromCurlResponse($response)
    {
        return substr($response, strrpos($response, "\r\n\r\n")+4, strlen($response));
    }

    private function checkResult($throwException)
    {
        $this->statusCode = curl_getinfo($this->curlSession, CURLINFO_HTTP_CODE);
        $this->contentType = curl_getinfo($this->curlSession, CURLINFO_CONTENT_TYPE);
        $this->setResultFields();
        if ($throwException === true) {
            $this->checkForCURLError();
            $this->checkForServerError();
        }
    }

    private function checkForCURLError()
    {
        if (curl_errno($this->curlSession)) {
            $curlErrorMessage = curl_error($this->curlSession);
            $curlErrorCode = curl_errno($this->curlSession);
            throw new MaileonAPIException(
                "An error occurred in the connection to the REST API. Original cURL error message: ${curlErrorMessage}",
                $curlErrorCode
            );
        }
    }

    private function checkForServerError()
    {
        $statusCode = $this->statusCode;
        if ($statusCode >= 500 && $statusCode <= 599) {
            throw new MaileonAPIException(
                "A server error occurred in the REST API (HTTP status code ${statusCode}).",
                $this->bodyData
            );
        }
    }

    private function setResultFields()
    {
        if ($this->bodyData) {
            // AddressCheck uses application/xml;charset=utf-8 content type
            if ($this->contentType == 'application/vnd.maileon.api+xml' ||
                $this->contentType == 'application/xml;charset=utf-8'
            ) {
                if ($this->startsWith(trim($this->bodyData), "<")) {
                        $this->resultXML = new \SimpleXMLElement($this->bodyData);
                        $this->result = XMLDeserializer::deserialize($this->resultXML);
                }
                if (!isset($this->result) && !is_array($this->result)) {
                        $this->result = $this->bodyData;
                }
            } elseif ($this->contentType == "application/json" ||
                $this->contentType == 'application/vnd.maileon.api+json'
            ) {
                $this->result = JSONDeserializer::json_decode($this->bodyData, $this->deserializationType);
            } else {
                $this->result = $this->bodyData;
            }
        }
    }

    /**
     * @return mixed
     *  the deserialized result object as a subclass of com_maileon_api_xml_AbstractXMLWrapper,
     *  or the free-form string result if the response body data was not a deserializable object,
     *  or null if there was no response body data
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return number
     *  the HTTP status code that was returned by the HTTP request
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return bool
     *  true iff a 2xx status code (success) was returned by the HTTP request
     */
    public function isSuccess()
    {
        return $this->statusCode >= 200 and $this->statusCode <= 299;
    }

    /**
     * @return bool
     *  true iff a 4xx status code (client error) was returned by the HTTP request
     */
    public function isClientError()
    {
        return $this->statusCode >= 400 and $this->statusCode <= 499;
    }

    /**
     * @return string
     *  the content type returned by the HTTP request
     */
    public function getContentType()
    {
        return $this->getContentType();
    }

    /**
     * @return string
     *  the unprocessed HTTP body data, or null if there was no body
     */
    public function getBodyData()
    {
        return $this->bodyData;
    }

    /**
     * @return \SimpleXMLElement
     *  the HTTP body data parsed as a SimpleXMLElement, or null if there was no XML in the body
     */
    public function getResultXML()
    {
        return $this->resultXML;
    }

    /**
     * @return array of strings
     *  an array of response headers
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @return string
     *  a human-readable representation of the HTTP request result
     */
    public function toString()
    {
        $result = "";
        $result .= "status code: " . $this->getStatusCode() . " ";
        $result .= HTTPResponseCodes::getStringFromHTTPStatusCode($this->getStatusCode()) . "\n";
        $result .= "is success: " . ($this->isSuccess() ? "true" : "false") . "\n";
        $result .= "is client error: " . ($this->isClientError() ? "true" : "false") . "\n";
        if ($this->bodyData) {
                $result .= "\nbody data:\n";
                $result .= $this->bodyData;
                $result .= "\n\n";
        } else {
                $result .= "No body data.\n";
        }
        if ($this->resultXML) {
                $result .= "Body contains XML.\n";
        }
        $resultType = gettype($this->result);
        if ($resultType == "object") {
                $result .= "Result type: " . get_class($this->result) . "\n";
        } else {
                $result .= "result type: " . $resultType . "\n";
        }
        return $result;
    }

    private function startsWith($haystack, $needle)
    {
            // search backwards starting from haystack length characters from the end
            return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}
