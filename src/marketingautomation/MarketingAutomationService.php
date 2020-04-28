<?php

namespace de\xqueue\maileon\api\client\marketingautomation;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIResult;

/**
 * Facade that wraps the REST service for marketing automation programs.
 *
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */

class MarketingAutomationService extends AbstractMaileonService
{
    /**
     * Starts a marketing automation program directly by its ID for a list of contacts (by email)
     * 
     * @param int $programId The ID of the MA program
     * @param array $emails A list of emails to start a program for. Can also be a single email (string)
     *
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function startMarketingAutomationProgram($programId, $emails)
    {
        $urlProgramId = urlencode($programId);

        if (!empty($emails)) {
            if (is_array($emails)) {
                $bodyContent['emails'] = $emails;
            } else {
                $bodyContent['emails'] = array($emails);
            }
        }

        return $this->post(
            "marketing-automation/$urlProgramId",
            json_encode($bodyContent),
            array(),
            "application/json"
        );
    }
}
