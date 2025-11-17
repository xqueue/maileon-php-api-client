<?php

namespace de\xqueue\maileon\api\client\contactfilters;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;

use function mb_convert_encoding;
use function rawurlencode;

// TODO explain contact filters

/**
 * Facade that wraps the REST service for contact filters.
 *
 * @author Felix Heinrichs
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class ContactfiltersService extends AbstractMaileonService
{
    /**
     * @return MaileonAPIResult|null The result object of the API call, with the count of defined contact filters available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getContactFiltersCount()
    {
        return $this->get('contactfilters/count');
    }

    /**
     * Returns the defined contact filters.
     *
     * @param int $page_index the paging index of the page to fetch
     * @param int $page_size  the number of entries to return per page
     *
     * @return MaileonAPIResult|null The result object of the API call, with a ContactFilter[] available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getContactFilters(
        $page_index = 1,
        $page_size = 100
    ) {
        $queryParameters = [
            'page_index' => $page_index,
            'page_size'  => $page_size,
        ];

        return $this->get(
            'contactfilters',
            $queryParameters
        );
    }

    /**
     * @param string $contactFilterId
     *
     * @return MaileonAPIResult|null The result object of the API call, with the ContactFilter available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getContactFilter($contactFilterId)
    {
        $encodedContactFilterId = rawurlencode(mb_convert_encoding((string) $contactFilterId, 'UTF-8'));

        return $this->get("contactfilters/contactfilter/$encodedContactFilterId");
    }

    /**
     * Updates a contact filter that is referenced by an ID.
     *
     * @param int           $contactFilterId the ID of the contact filter to update
     * @param ContactFilter $newFilterObject the new data. Currently, the only field that is actually updated is the name of the filter.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function updateContactFilter(
        $contactFilterId,
        $newFilterObject
    ) {
        $encodedContactFilterId = rawurlencode(mb_convert_encoding((string) $contactFilterId, 'UTF-8'));

        return $this->post(
            "contactfilters/contactfilter/$encodedContactFilterId",
            $newFilterObject->toXMLString()
        );
    }

    /**
     * Creates a simple contact filter.
     *
     * @param ContactFilter    $newFilterObject   The data for the filter
     * @param bool             $createTargetGroup if true, also a target group will be created and the ID will be returned
     * @param int|float|string $version           version identifier to use different versions of the created target group resource
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function createContactFilter(
        $newFilterObject,
        $createTargetGroup,
        $version = 1.0
    ) {
        if ($version === 1.0) {
            $queryParameters = ['createTargetGroup' => $createTargetGroup === true ? 'true' : 'false'];

            return $this->put(
                'contactfilters/contactfilter',
                $newFilterObject->toXMLString(),
                $queryParameters
            );
        }

        if ($version === 2.0) {
            $queryParameters = ['createTargetGroup' => $createTargetGroup === true ? 'true' : 'false'];

            return $this->post(
                'contactfilters/v2',
                $newFilterObject,
                $queryParameters,
                'application/json'
            );
        }

        return null;
    }

    /**
     * Deletes a contact filter that is referenced by an ID.
     *
     * @param int $contactFilterId the ID of the contact filter
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteContactFilter($contactFilterId)
    {
        $encodedContactFilterId = rawurlencode(mb_convert_encoding((string) $contactFilterId, 'UTF-8'));

        return $this->delete("contactfilters/contactfilter/$encodedContactFilterId");
    }

    /**
     * Causes a refresh of the contact filter referenced by an ID. This means that the result set of
     * contacts matched by the contact filter is recomputed.
     *
     * @param int $contactFilterId The ID of the contact filter to refresh
     * @param     $time            A timestamp for the request. If the contact filter was updated after the given timestamp, the refresh is
     *                             not performed. The default value will force the refresh to always be performed.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function refreshContactFilterContacts(
        $contactFilterId,
        $time
    ) {
        $encodedContactFilterId = rawurlencode(mb_convert_encoding((string) $contactFilterId, 'UTF-8'));

        return $this->get(
            "contactfilters/contactfilter/$encodedContactFilterId/refresh",
            $time ? ['time' => $time] : null
        );
    }
}
