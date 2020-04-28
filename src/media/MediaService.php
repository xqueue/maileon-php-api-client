<?php

namespace de\xqueue\maileon\api\client\media;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIResult;

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
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function getMailingTemplates()
    {
        return $this->get(
            "media/templates/mailings",
            null,
            'application/vnd.maileon.api+json',
            'array'
        );
    }
}
