<?php

namespace de\xqueue\maileon\api\client\Blacklists;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use de\xqueue\maileon\api\client\blacklists\AddEntriesAction;

/**
 * Facade that wraps the REST service for blacklists.
 *
 */
class BlacklistsService extends AbstractMaileonService
{

    /**
     * Retrieves the names and IDs of the custom blacklists in your account.
     *
     * @return MaileonAPIResult
     * the result object of the API call, with a Blacklist[] available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function getBlacklists()
    {
        return $this->get("blacklists");
    }

    /**
     * Retrieves a full blacklist (including entries) by id.
     *
     * @param integer $id
     * the id of the blacklist to retrieve
     * @return MaileonAPIResult
     * the result object of the API call, with the requested Blacklist available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function getBlacklist($id)
    {
        return $this->get("blacklists/" . $id);
    }

    /**
     * Adds a number of expressions to a blacklist.
     *
     * @param integer $id
     * the id of the blacklist to add the entries to
     * @param string[] $entries
     * the blacklist entries to add to the blacklist
     * @param string $importName
     * a unique name for the import that will be displayed in Maileon's web user interface.
     * If this is null, a unique name will be generated.
     * @return MaileonAPIResult
     * the result object of the API call.
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function addEntriesToBlacklist($id, $entries, $importName = null)
    {
        if ($importName == null) {
            $importName = "phpclient_import_" . uniqid();
        }
        $action = new AddEntriesAction();
        $action->importName = $importName;
        $action->entries = $entries;
        return $this->post("blacklists/" . $id . "/actions", $action->toXMLString());
    }
}
