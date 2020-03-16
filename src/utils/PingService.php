<?php

namespace de\xqueue\maileon\api\client\Utils;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;

/**
 * A facade that wraps the REST "ping" system interface monitoring service.
 *
 * @author Felix Heinrichs | Trusted Mails GmbH |
 * <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class PingService extends AbstractMaileonService
{
    const PING_RESOURCE = "ping";

    /**
     * Tests if sending GET requests to the REST API works.
     *
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function pingGet()
    {
        return $this->get(self::PING_RESOURCE);
    }

    /**
     * Tests if sending PUT requests to the REST API works.
     *
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function pingPut()
    {
        return $this->put(self::PING_RESOURCE, "");
    }

    /**
     * Tests if sending POST requests to the REST API works.
     *
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function pingPost()
    {
        return $this->post(self::PING_RESOURCE, "foobar");
    }

    /**
     * Tests if sending DELETE requests to the REST API works.
     *
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function pingDelete()
    {
        return $this->delete(self::PING_RESOURCE);
    }

    /**
     * Convenience method to check whether the API call succeeded or not.
     *
     * @param array $result
     *  a result wrapper as returned from one of the ping methods in this class
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function checkResult($result)
    {
        return ($result['STATUS_CODE'] == '200');
    }
}
