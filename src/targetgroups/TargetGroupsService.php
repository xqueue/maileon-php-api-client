<?php

namespace de\xqueue\maileon\api\client\targetgroups;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIResult;

/**
 * Facade that wraps the REST service for target groups
 *
 * @author Marcus St&auml;nder
 */
class TargetGroupsService extends AbstractMaileonService
{


    /**
     * @return MaileonAPIResult
     *    the result object of the API call, with the count of defined target groups available
     *  at MaileonAPIResult::getResult()
     */
    public function getTargetGroupsCount()
    {
        return $this->get('targetgroups/count');
    }

    /**
     * Returns the defined target groups.
     *
     * @param number $page_index
     *  the paging index of the page to fetch
     * @param number $page_size
     *  the number of entries to return per page
     * @return MaileonAPIResult
     *    the result object of the API call, with a TargetGroup
     *  available at MaileonAPIResult::getResult()
     */
    public function getTargetGroups($page_index = 1, $page_size = 10)
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size
        );
        return $this->get('targetgroups', $queryParameters);
    }

    /**
     * @param string $targetGroupId
     * @return MaileonAPIResult
     *    the result object of the API call, with the TargetGroup
     *  available at MaileonAPIResult::getResult()
     */
    public function getTargetGroup($targetGroupId)
    {
        return $this->get('targetgroups/targetgroup/' . $targetGroupId);
    }

    /**
     * Create a target group
     * @param TargetGroup $targetGroup
     * @return MaileonAPIResult
     *    the result object of the API call, with the TargetGroup
     *  available at MaileonAPIResult::getResult()
     */
    public function createTargetGroup($targetGroup)
    {
        return $this->post('targetgroups', $targetGroup->toXMLString());
    }

    /**
     * @param string $targetGroupId
     * @return MaileonAPIResult
     *  the result object of the API call, with the deleted TargetGroup
     *  available at MaileonAPIResult::getResult()
     */
    public function deleteTargetGroup($targetGroupId)
    {
        return $this->delete('targetgroups/targetgroup/' . $targetGroupId);
    }
}
