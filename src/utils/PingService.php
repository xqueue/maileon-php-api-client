<?php

namespace de\xqueue\maileon\api\client\utils;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;

/**
 * A facade that wraps the REST "ping" system interface monitoring service.
 *
 * @author Felix Heinrichs
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class PingService extends AbstractMaileonService
{
    const PING_RESOURCE = 'ping';

    /**
     * Tests if sending GET requests to the REST API works.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function pingGet()
    {
        return $this->get(self::PING_RESOURCE);
    }

    /**
     * Tests if sending PUT requests to the REST API works.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function pingPut()
    {
        return $this->put(self::PING_RESOURCE);
    }

    /**
     * Tests if sending POST requests to the REST API works.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function pingPost()
    {
        return $this->post(
            self::PING_RESOURCE,
            'foobar'
        );
    }

    /**
     * Tests if sending DELETE requests to the REST API works.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function pingDelete()
    {
        return $this->delete(self::PING_RESOURCE);
    }

    /**
     * Convenience method to check whether the API call succeeded or not.
     *
     * @param array $result a result wrapper as returned from one of the ping methods in this class
     *
     * @return bool the result object of the API call
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function checkResult($result): bool
    {
        return $result['STATUS_CODE'] === '200';
    }
}
