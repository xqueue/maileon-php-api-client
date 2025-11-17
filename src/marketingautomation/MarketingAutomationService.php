<?php

namespace de\xqueue\maileon\api\client\marketingautomation;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;

use function is_array;
use function json_encode;
use function mb_convert_encoding;
use function rawurlencode;

/**
 * Facade that wraps the REST service for marketing automation programs.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class MarketingAutomationService extends AbstractMaileonService
{
    /**
     * Starts a marketing automation program directly by its ID for a list of contacts (by email)
     *
     * @param int   $programId The ID of the MA program
     * @param array $emails    A list of emails to start a program for. Can also be a single email (string)
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function startMarketingAutomationProgram(
        $programId,
        $emails
    ) {
        $encodedProgramId = rawurlencode(mb_convert_encoding((string) $programId, 'UTF-8'));

        if (! empty($emails)) {
            if (is_array($emails)) {
                $bodyContent['emails'] = $emails;
            } else {
                $bodyContent['emails'] = [$emails];
            }
        }

        return $this->post(
            "marketing-automation/$encodedProgramId",
            isset($bodyContent) ? json_encode($bodyContent) : '',
            [],
            'application/json'
        );
    }
}
