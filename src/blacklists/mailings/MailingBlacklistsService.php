<?php

namespace de\xqueue\maileon\api\client\blacklists\mailings;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;

/**
 * Facade that wraps the REST service for mailing blacklists.
 *
 */
class MailingBlacklistsService extends AbstractMaileonService
{

    /**
     * Retrieves the IDs, names, create times and users who created the mailing blacklists.
     *
     * @param number $page_index
     *  the index of the result page to fetch
     * @param number $page_size
     *  the number of results to fetch per page, maximum is 1000
     *
     * @return MaileonAPIResult
     * the result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function getMailingBlacklists($page_index = 1, $page_size = 100)
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size,
        );

        return $this->get("mailingblacklists", $queryParameters);
    }

    /**
     * Retrieves mailing blacklist details by id.
     *
     * @param integer $id
     * the id of the mailing blacklist to retrieve
     *
     * @return MaileonAPIResult
     * the result object of the API call, with the requested MailingBlacklist available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function getMailingBlacklist($id)
    {
        return $this->get("mailingblacklists/" . $id);
    }

    /**
     * Creates a mailing blacklist with the given name..
     *
     * @param string $name
     * the name of the mailing blacklist to create
     *
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function createMailingBlacklist($name)
    {
        $queryParameters = array(
            'name' => urlencode($name),
        );
        return $this->post("mailingblacklists/", "", $queryParameters);
    }

    /**
     * Updates the mailing blacklist name for the list with the given ID.
     *
     * @param integer $id
     * the id of the mailing blacklist to update
     *
     * @param string $name
     * the name to set
     *
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function updateMailingBlacklist($id, $name)
    {
        $queryParameters = array(
            'name' => urlencode($name),
        );
        return $this->put("mailingblacklists/" . $id, "", $queryParameters);
    }

    /**
     * Deletes a mailing blacklist
     *
     * @param integer $id
     * the id of the mailing blacklist to delete
     *
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function deleteMailingBlacklist($id)
    {
        return $this->delete("mailingblacklists/" . $id);
    }

    /**
     * Adds a number of expressions to a mailing blacklist.
     *
     * @param integer $id
     * the id of the mailing blacklist to add the entries to
     * @param MailingBlacklistExpressions $expressions
     * the mailing blacklist expressions to add to the mailing blacklist
     *
     * @return MaileonAPIResult
     * the result object of the API call.
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function addEntriesToBlacklist($id, $expressions)
    {
        return $this->post("mailingblacklists/" . $id . "/expressions", $expressions->toXMLString());
    }

    /**
     * Gets the expressions for a mailing blacklist.
     *
     * @param integer $id
     *  The ID of the mailing blacklist
     * @param integer $page_index
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $page_size
     *  The maximum count of items in the result page. If provided, the value of page_size must be in the range 1 to 1000.
     *
     * @return MaileonAPIResult
     * the result object of the API call.
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function getEntriesForBlacklist($id, $page_index = 1, $page_size = 100)
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size
        );

        return $this->get("mailingblacklists/" . $id . "/expressions", $queryParameters, "application/json", [
            'array',
            MailingBlacklistExpression::class
        ]);
    }
}

