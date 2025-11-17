<?php

namespace de\xqueue\maileon\api\client\blacklists;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;

use function mb_convert_encoding;
use function rawurlencode;
use function uniqid;

/**
 * Facade that wraps the REST service for blacklists.
 *
 */
class BlacklistsService extends AbstractMaileonService
{

    /**
     * Retrieves the names and IDs of the custom blacklists in your account.
     *
     * @return MaileonAPIResult|null The result object of the API call, with a Blacklist[] available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getBlacklists()
    {
        return $this->get('blacklists');
    }

    /**
     * Retrieves a full blacklist (including entries) by id.
     *
     * @param int $id the id of the blacklist to retrieve
     *
     * @return MaileonAPIResult|null The result object of the API call, with the requested Blacklist available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getBlacklist($id)
    {
        $encodedId = rawurlencode(mb_convert_encoding((string) $id, 'UTF-8'));

        return $this->get("blacklists/$encodedId");
    }

    /**
     * Adds a number of expressions to a blacklist.
     *
     * @param int      $id         the id of the blacklist to add the entries to
     * @param string[] $entries    the blacklist entries to add to the blacklist
     * @param string   $importName a unique name for the import that will be displayed in Maileon's web user interface. If this is null, a
     *                             unique name will be generated.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult().
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function addEntriesToBlacklist(
        $id,
        $entries,
        $importName = null
    ) {
        $encodedId = rawurlencode(mb_convert_encoding((string) $id, 'UTF-8'));

        if ($importName === null) {
            $importName = 'phpclient_import_' . uniqid();
        }

        $action             = new AddEntriesAction();
        $action->importName = $importName;
        $action->entries    = $entries;

        return $this->post(
            "blacklists/$encodedId/actions",
            $action->toXMLString()
        );
    }
}
