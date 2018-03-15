<?php

namespace XQueue\Maileon\API\TargetGroups;

use XQueue\Maileon\API\AbstractMaileonService;

/**
 * Facade that wraps the REST service for target groups
 *
 * @author Marcus St&auml;nder
 */
class TargetGroupsService extends AbstractMaileonService
{


    /**
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the count of defined target groups available
     *  at com_maileon_api_MaileonAPIResult::getResult()
     */
    function getTargetGroupsCount()
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
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with a com_maileon_api_targetgroups_TargetGroup
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     */
    function getTargetGroups($page_index = 1, $page_size = 10)
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size
        );
        return $this->get('targetgroups', $queryParameters);
    }

    /**
     * @param string $targetGroupId
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the com_maileon_api_targetgroups_TargetGroup
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     */
    function getTargetGroup($targetGroupId)
    {
        return $this->get('targetgroups/targetgroup/' . $targetGroupId);
    }

    /**
     * Create a target group
     * @param com_maileon_api_targetgroups_TargetGroup $targetGroup
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the com_maileon_api_targetgroups_TargetGroup
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     */
    function createTargetGroup($targetGroup)
    {
        return $this->post('targetgroups', $targetGroup->toXMLString());
    }

    /**
     * @param string $targetGroupId
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result object of the API call, with the deleted com_maileon_api_targetgroups_TargetGroup
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     */
    function deleteTargetGroup($targetGroupId)
    {
        return $this->delete('targetgroups/targetgroup/' . $targetGroupId);
    }
}