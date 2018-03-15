<?php

namespace XQueue\Maileon\API\ContactFilters;

use XQueue\Maileon\API\AbstractMaileonService;

// TODO explain contact filters
/**
 * Facade that wraps the REST service for contact filters.
 *
 * @author Felix Heinrichs | Trusted Mails GmbH | <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class ContactfiltersService extends AbstractMaileonService
{

    /**
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the count of defined contact filters available
     *  at com_maileon_api_MaileonAPIResult::getResult()
     */
    function getContactFiltersCount()
    {
        return $this->get('contactfilters/count');
    }

    /**
     * Returns the defined contact filters.
     *
     * @param number $page_index
     *  the paging index of the page to fetch
     * @param number $page_size
     *  the number of entries to return per page
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with a com_maileon_api_contactfilters_ContactFilter[]
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     */
    function getContactFilters($page_index = 1, $page_size = 10)
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size
        );
        return $this->get('contactfilters', $queryParameters);
    }

    /**
     * @param string $contactFilterId
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the com_maileon_api_contactfilters_ContactFilter
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     */
    function getContactFilter($contactFilterId)
    {
        return $this->get('contactfilters/contactfilter/' . $contactFilterId);
    }

    /**
     * Updates a contact filter that is referenced by an ID.
     *
     * @param contactFilterId
     *  the ID of the contact filter to update
     * @param com_maileon_api_contactfilters_ContactFilter $newFilterObject
     *  the new data. Currently, the only field that is actually updated is the name of the filter.
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     */
    function updateContactFilter($contactFilterId, $newFilterObject)
    {
        return $this->post("contactfilters/contactfilter/" . $contactFilterId, $newFilterObject->toXMLString());
    }

    /**
     * Creates a simple contact filter.
     *
     * @param com_maileon_api_contactfilters_ContactFilter $newFilterObject
     *  the data for the filter
     * @param bool $createTargetGroup
     *  if true, also a target group will be created and the ID will be returned
     * @param number $version
     *  version identifyer to use different versions of the create targetgroup resource
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     */
    function createContactFilter($newFilterObject, $createTargetGroup, $version = 1.0)
    {
        if ($version == 1.0) {
            $queryParameters = array(
                'createTargetGroup' => ($createTargetGroup) ? "true" : "false"
            );
            return $this->put("contactfilters/contactfilter", $newFilterObject->toXMLString(), $queryParameters);
        } else if ($version == 2.0) {
            $queryParameters = array(
                'createTargetGroup' => ($createTargetGroup) ? "true" : "false"
            );
            return $this->post("contactfilters/v2", $newFilterObject, $queryParameters, "application/json");
        }
    }

    /**
     * Deletes a contact filter that is referenced by an ID.
     *
     * @param contactFilterId
     *  the ID of the contact filter
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     */
    function deleteContactFilter($contactFilterId)
    {
        return $this->delete("contactfilters/contactfilter/" . $contactFilterId);
    }

    /**
     * Causes a refresh of the contact filter referenced by an ID. This means that the result set of
     * contacts matched by the contact filter is recomputed.
     *
     * @param contactFilterId
     *  the ID of the contact filter to refresh
     * @param time
     *  a timestamp for the request. If the contact filter was updated after the given timestamp,
     *  the refresh is not performed. The default value will force the refresh to always be performed.
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     */
    function refreshContactFilterContacts($contactFilterId, $time)
    {
        return $this->get("contactfilters/contactfilter/" . $contactFilterId . "/refresh", ($time) ? array("time" => $time) : null);
    }
}