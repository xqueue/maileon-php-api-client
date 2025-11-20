<?php

namespace de\xqueue\maileon\api\client;

use de\xqueue\maileon\api\client\json\JSONDeserializer;
use de\xqueue\maileon\api\client\xml\XMLDeserializer;
use Exception;
use SimpleXMLElement;

use function array_change_key_case;
use function curl_errno;
use function curl_error;
use function curl_getinfo;
use function explode;
use function get_class;
use function gettype;
use function is_array;
use function strlen;
use function strpos;
use function strrpos;
use function strtolower;
use function substr;
use function trim;

/**
 * The result of making a call to the Maileon REST API.
 *
 * This class encapsulates the technical details of the REST API's HTTP response. In order
 * to retrieve the payload result in deserialized form, just call MaileonAPIResult::getResult().
 *
 * However, this class also allows the underlying HTTP response information to be queried,
 * including the returned status code (MaileonAPIResult::getStatusCode())
 * and content type (MaileonAPIResult::getContentType()) as well as the raw
 * HTTP response body data (MaileonAPIResult::getBodyData()).
 */
class MaileonAPIResult
{
    private $curlSession;

    private $statusCode;
    private $contentType;

    private $bodyData;
    private $responseHeaders;
    private $resultXML;
    private $result;

    private $deserializationType;

    /**
     * Creates a new result object from the curl response and session data.
     *
     * @param string $response            the HTTP response data, null if there was none
     * @param mixed  $curlSession         the cURL session that was used
     * @param bool   $throwException      if true, an exception will be thrown in case of a connection or server error
     * @param mixed  $deserializationType The name of the class this result should be deserialized as. Use array( 'array', 'typename' ) to
     *                                    deserialize arrays of a type.
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred (only if $throwException == true)
     */
    public function __construct(
        $response,
        $curlSession,
        $throwException = true,
        $deserializationType = null
    ) {
        $this->bodyData            = $this->getBodyFromCurlResponse($curlSession, $response);
        $this->curlSession         = $curlSession;
        $this->deserializationType = $deserializationType;
        $this->responseHeaders     = $this->getHeaderArrayFromCurlResponse($curlSession, $response);
        $this->checkResult($throwException);
    }

    private function getBodyFromCurlResponse(
        $curlSession,
        $response
    ) {
        if ($response === null) {
            return null;
        }

        // In a recent case, a CMS2 mailing contained \r\n\r\n, so the old approach failed (https://stackoverflow.com/questions/10589889/returning-header-as-array-using-curl).
        // Now, we use CURLINFO_HEADER_SIZE (https://blog.devgenius.io/how-to-get-the-response-headers-with-curl-in-php-2173b10d4fc5) and only split up the headers at \r\n\r\n.
        // CURLINFO_HEADER_SIZE returns the size of the header including \r\n\r\n.
        $headerSize = curl_getinfo($curlSession, CURLINFO_HEADER_SIZE);

        return substr($response, $headerSize);
    }

    private function getHeaderArrayFromCurlResponse(
        $curlSession,
        $response
    ): array {
        $headers = [];

        $headerSize = curl_getinfo($curlSession, CURLINFO_HEADER_SIZE);

        // The header section is separated by \n\r\n\r, so trim those 4 bytes from the header as we do not need them
        $header_text = substr($response, 0, $headerSize - 4);

        // Check if there is a proxy header section. If so, select last header section (from Maileon)
        // Maybe it makes sense to return an array with one entry for each header section (proxies, then normal headers), each containing the entries of the appropriate header section.
        // As this is not backwards compatible, skip for now.
        if (strpos($header_text, "\r\n\r\n") !== false) {
            $start       = strrpos($header_text, "\r\n\r\n") + 4;
            $header_text = substr($header_text, $start);
        }

        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } elseif (strpos($line, ':') != 0) {
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    /**
     * @param $throwException
     *
     * @return void
     *
     * @throws Exception
     */
    private function checkResult($throwException)
    {
        $this->statusCode  = curl_getinfo($this->curlSession, CURLINFO_HTTP_CODE);
        $this->contentType = curl_getinfo($this->curlSession, CURLINFO_CONTENT_TYPE);
        $this->setResultFields();

        if ($throwException === true) {
            $this->checkForCURLError();
            $this->checkForServerError();
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    private function setResultFields()
    {
        if ($this->bodyData) {
            // AddressCheck uses application/xml;charset=utf-8 content type
            if ($this->contentType === 'application/vnd.maileon.api+xml'
                || $this->contentType === 'application/xml;charset=utf-8'
                || $this->contentType === 'application/xml'
            ) {
                if ($this->startsWith(trim($this->bodyData), '<')) {
                    $this->resultXML = new SimpleXMLElement($this->bodyData);
                    $this->result    = XMLDeserializer::deserialize($this->resultXML);
                }

                if (! isset($this->result) && ! is_array($this->result)) {
                    $this->result = $this->bodyData;
                }
            } elseif ($this->contentType === 'application/json'
                || $this->contentType === 'application/vnd.maileon.api+json'
            ) {
                $this->result = JSONDeserializer::json_decode($this->bodyData, $this->deserializationType);
            } else {
                $this->result = $this->bodyData;
            }
        }
    }

    private function startsWith(
        $haystack,
        $needle
    ): bool {
        // search backwards starting from haystack length characters from the end
        return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    private function checkForCURLError()
    {
        if (curl_errno($this->curlSession)) {
            $curlErrorMessage = curl_error($this->curlSession);
            $curlErrorCode    = curl_errno($this->curlSession);
            throw new MaileonAPIException(
                "An error occurred in the connection to the REST API. Original cURL error message: $curlErrorMessage",
                $curlErrorCode
            );
        }
    }

    private function checkForServerError()
    {
        $statusCode = $this->statusCode;

        if ($statusCode >= 500 && $statusCode <= 599) {
            throw new MaileonAPIException(
                "A server error occurred in the REST API (HTTP status code $statusCode).",
                $this->bodyData
            );
        }
    }

    /**
     * @return mixed The deserialized result object as a subclass of AbstractXMLWrapper, or the free-form string result if the response body data was not a deserializable object, or null if there was no response body data
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string The content type returned by the HTTP request
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return string The unprocessed HTTP body data, or null if there was no body
     */
    public function getBodyData()
    {
        return $this->bodyData;
    }

    /**
     * @return SimpleXMLElement|null  The HTTP body data parsed as a SimpleXMLElement, or null if there was no XML in the body
     */
    public function getResultXML()
    {
        return $this->resultXML;
    }

    /**
     * @return array an array of response headers (strings)
     */
    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    /**
     * @param string $header
     *
     * @return bool
     */
    public function hasResponseHeader($header): bool
    {
        $responseHeaders = array_change_key_case($this->responseHeaders);

        return isset($responseHeaders[strtolower($header)]);
    }

    /**
     * @param string $header
     *
     * @return string|null
     */
    public function getResponseHeader($header)
    {
        $responseHeaders = array_change_key_case($this->responseHeaders);

        return $responseHeaders[strtolower($header)] ?? null;
    }

    public function toString(): string
    {
        $result = 'status code: ' . $this->getStatusCode() . ' ';
        $result .= HTTPResponseCodes::getStringFromHTTPStatusCode($this->getStatusCode()) . "\n";
        $result .= 'is success: ' . ($this->isSuccess() ? 'true' : 'false') . "\n";
        $result .= 'is client error: ' . ($this->isClientError() ? 'true' : 'false') . "\n";

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

        if ($resultType === 'object') {
            $result .= 'Result type: ' . get_class($this->result) . "\n";
        } else {
            $result .= 'Result type: ' . $resultType . "\n";
        }

        return $result;
    }

    /**
     * @return int The HTTP status code that was returned by the HTTP request
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return bool true if a 2xx status code (success) was returned by the HTTP request
     */
    public function isSuccess(): bool
    {
        return $this->statusCode >= 200 and $this->statusCode <= 299;
    }

    /**
     * @return bool true if a 4xx status code (client error) was returned by the HTTP request
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 and $this->statusCode <= 499;
    }
}
