<?php

namespace de\xqueue\maileon\api\client\targetgroups;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;

use function mb_convert_encoding;
use function rawurlencode;

/**
 * Facade that wraps the REST service for target groups
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class TargetGroupsService extends AbstractMaileonService
{
    /**
     * @return MaileonAPIResult|null The result object of the API call, with the count of defined target groups available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTargetGroupsCount()
    {
        return $this->get('targetgroups/count');
    }

    /**
     * Returns the defined target groups.
     *
     * @param int $page_index The paging index of the page to fetch
     * @param int $page_size  The number of entries to return per page
     *
     * @return MaileonAPIResult|null The result object of the API call, with a TargetGroup available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTargetGroups(
        $page_index = 1,
        $page_size = 10
    ) {
        $queryParameters = [
            'page_index' => $page_index,
            'page_size'  => $page_size,
        ];

        return $this->get(
            'targetgroups',
            $queryParameters
        );
    }

    /**
     * @param string $targetGroupId
     *
     * @return MaileonAPIResult|null The result object of the API call, with the TargetGroup available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTargetGroup($targetGroupId)
    {
        $encodedTargetGroupId = rawurlencode(mb_convert_encoding((string) $targetGroupId, 'UTF-8'));

        return $this->get("targetgroups/targetgroup/$encodedTargetGroupId");
    }

    /**
     * Create a target group
     *
     * @param TargetGroup $targetGroup
     *
     * @return MaileonAPIResult|null The result object of the API call, with the TargetGroup available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function createTargetGroup($targetGroup)
    {
        return $this->post(
            'targetgroups',
            $targetGroup->toXMLString()
        );
    }

    /**
     * @param string $targetGroupId
     *
     * @return MaileonAPIResult|null The result object of the API call, with the deleted TargetGroup available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteTargetGroup($targetGroupId)
    {
        $encodedTargetGroupId = rawurlencode(mb_convert_encoding((string) $targetGroupId, 'UTF-8'));

        return $this->delete("targetgroups/targetgroup/$encodedTargetGroupId");
    }
}
