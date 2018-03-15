<?php

namespace XQueue\Maileon\API\MarketingAutomation;

use XQueue\Maileon\API\AbstractMaileonService;

/**
 * Facade that wraps the REST service for marketing automation programs.
 * 
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 * @author Marcus St&auml;nder | Trusted Technologies GmbH | <a href="mailto:marcus.staender@trusted-technologies.de">marcus.staender@trusted-technologies.de</a>
 */

class MarketingAutomationService extends AbstractMaileonService
{
	/**
	 * Creates a new mailing.
	 * @param int $programId
	 *
	 * @param string $email
	 *
	 * @return \em com_maileon_api_MaileonAPIResult
	 *  the result of the operation
	 */
	public function startMarketingAutomationProgram($programId, $emails) {
		$urlProgramId = urlencode($programId);

		if (!empty($emails)) {
			if (is_array($emails)) {
				$bodyContent['emails'] = $emails;
			} else {
				$bodyContent['emails'] = array($emails);
			}
		}


		return $this->post("marketing-automation/$urlProgramId", json_encode($bodyContent), array(), "application/json");
	}
}