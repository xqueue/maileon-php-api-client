<?php

namespace de\xqueue\maileon\api\client\media;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;

/**
 * Facade that wraps the REST service for media resources.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class MediaService extends AbstractMaileonService
{
    /**
     * Retrieves a list of mailing templates from an account
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred The result of the operation
     */
    public function getMailingTemplates()
    {
        return $this->get(
            'media/templates/mailings',
            null,
            'application/vnd.maileon.api+json',
            'array'
        );
    }
}
