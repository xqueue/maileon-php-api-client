<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;

/**
 * Facade that wraps the REST service for mailings.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 * @author Andreas Lange | XQueue GmbH | <a href="mailto:andreas.lange@xqueue.com">andreas.lange@xqueue.com</a>
 */
class MailingsService extends AbstractMaileonService
{
    /**
     * Creates a new mailing.
     * @param string $name
     *  the name of the mailing
     * @param string $subject
     *  the subject of the mailing
     * @param bool $deprecatedParameter
     *  this parameter was never used by the API
     * @param string $type
     *  the type of the mailing, which can be one of 'doi', 'trigger', or 'regular'.
     * @param string $editorVersion
     *  the version of the CMS to create the mailing for.
     *  Valid values for CMS1: 'v1', '1'. 
     *  Valid values for CMS2: 'v2', '2'. 
     *  By default (no value), the mailing will be created as a CMS2 template, if CMS2 is activated.
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function createMailing($name, $subject, $deprecatedParameter = false, $type = "regular", $editorVersion = "")
    {
        $queryParameters = array(
            'name' => urlencode($name),
            'subject' => urlencode($subject),
            'type' => urlencode($type),
            'editorVersion' => urlencode($editorVersion)
        );
        
        return $this->post('mailings', "", $queryParameters);
    }
    
    /**
     * Get the ID of a mailing by its name
     * 
     * @param integer $mailingId the ID of the mailing
     */
    public function getMailingIdByName($mailingName)
    {
        return $this->get('mailings/name/' . rawurlencode($mailingName));
    }
    
    /**
     * Get the type of a mailing. It can be either 'doi', 'trigger', or 'regular
     * 
     * @param integer $mailingId the ID of the mailing
     * @return \de\xqueue\maileon\api\client\MaileonAPIResult
     */
    public function getType($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/type');
    }
    
    /**
     * Check if a mailing with the given name exists and return true or false
     * 
     * @param integer $mailingId the ID of the mailing
     */
    public function checkIfMailingExistsByName($mailingName)
    {
        $response = $this->get('mailings/name/' . rawurlencode($mailingName));
        return ($response->isSuccess());
    }
    
    /**
     * Disable all QoS checks for a given mailing
     * 
     * @param integer $mailingId the ID of the mailing
     */
    public function disableQosChecks($mailingId)
    {
        return $this->put('mailings/' . $mailingId . '/settings/disableQosChecks');
    }

    /**
     * Set ignoring permissions for sendouts.
     * This is only possible with transaction/trigger mails and be aware to NOT add advertisements
     * in mails you send without advertisement permission. This is meant for order confirmations and the like.
     *
     * @param integer $mailingId the ID of the mailing
     * @param boolean $ignorePermission can be either true or false
     */
    public function setIgnorePermission($mailingId, $ignorePermission)
    {
        if ($ignorePermission === true) {
            $ignorePermission = "true";
        } elseif ($ignorePermission === false) {
            $ignorePermission = "false";
        }
        return $this->post(
            'mailings/' . $mailingId . '/settings/ignorepermission',
            "<ignore_permission>$ignorePermission</ignore_permission>"
        );
    }
    
    /**
     * Check if a (trigger) mail is set to ignore permission during sendout (order confirmation, invoices, ...)
     * 
     * @param integer $mailingId the ID of the mailing
     * @return "true" or "false"
     */
    public function isIgnorePermission($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/settings/ignorepermission');
    }
    
    /**
     * Set cleanup option for post sendout processing.
     * This flag defines if the used contact list and filter should be deleted after sendout.
     *
     * @param integer $mailingId the ID of the mailing
     * @param boolean $cleanup can be either true or false
     */
    public function setCleanupListsAndFilters($mailingId, $cleanup)
    {
        if ($cleanup === true) {
            $cleanup = "true";
        } elseif ($cleanup === false) {
            $cleanup = "false";
        }
        return $this->post(
            'mailings/' . $mailingId . '/settings/post_sendout_cleanup',
            "<cleanup>$cleanup</cleanup>"
            );
    }
    
    /**
     * Retrieve the cleanup option for post sendout processing.
     * This flag defines if the used contact list and filter should be deleted after sendout.
     *
     * @param integer $mailingId the ID of the mailing
     * @return "true" or "false"
     */
    public function isCleanupListsAndFilters($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/settings/post_sendout_cleanup');
    }
    
    /**
     * Sets the dispatch logic for trigger mailings
     * 
     * @param integer $mailingId the ID of the mailing
     * @param string $logic the string representation of the logic (xml)
     */
    public function setTriggerDispatchLogic($mailingId, $logic)
    {
        $queryParameters = array();
        return $this->put('mailings/' . $mailingId . '/dispatching', $logic, $queryParameters);
    }
    
    /**
     * Used for DOI Mailings
     * 
     * @param integer $mailingId the ID of the mailing
     * */
    public function setTriggerActive($mailingId)
    {
        return $this->post('mailings/' . $mailingId . '/dispatching/activate', "");
    }
    
    /**
     * Deletes an active trigger mailing.
     * 
     * @param integer $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function deleteActiveTriggerMailing($mailingId)
    {
        return $this->delete("mailings/" . $mailingId . "/dispatching");
    }
    
    /**
     * Deletes a mailing by ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function deleteMailing($mailingId)
    {
        return $this->delete("mailings/" . $mailingId);
    }
    
    /**
     * Updates the HTML content of the mailing referenced by the given ID.
     *
     * @param integer $mailingId the ID of the mailing
     * @param string $html
     *  the new HTML content of the mailing
     * @param bool $doImageGrabbing
     *  specifies if image grabbing should be performed
     * @param bool $doLinkTracking
     *  specifies if link tracking should be performed
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setHTMLContent($mailingId, $html, $doImageGrabbing = true, $doLinkTracking = false)
    {
        $queryParameters = array(
            'doImageGrabbing' => ($doImageGrabbing == true) ? "true" : "false",
            'doLinkTracking' => ($doLinkTracking == true) ? "true" : "false"
        );
        return $this->post('mailings/' . $mailingId . '/contents/html', $html, $queryParameters, "text/html");
    }
    
    /**
     * Updates the TEXT content of the mailing referenced by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @param string $text
     *  the new TEXT content of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setTextContent($mailingId, $text)
    {
        return $this->post('mailings/' . $mailingId . '/contents/text', $text, array(), "text/plain");
    }
    
    /**
     * Fetches the HTML content of the mailing identified by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call, with the HTML content string of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getHTMLContent($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/html', null, "text/html");
    }
    
    /**
     * Fetches the TEXT content of the mailing identified by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call, with the TEXT content string of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getTextContent($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/text', null, "text/plain");
    }
    
    /**
     * Updates the target group id of the mailing referenced by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @param string $targetGroupId
     *  the ID of the target group to set
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setTargetGroupId($mailingId, $targetGroupId)
    {
        return $this->post(
            'mailings/' . $mailingId . '/targetgroupid',
            "<targetgroupid>" . $targetGroupId . "</targetgroupid>"
        );
    }
    
    /**
     * Fetches the target group id of the mailing identified by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call, with the target group id of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getTargetGroupId($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/targetgroupid', null);
    }
    
    /**
     * Updates the sender email address of the mailing referenced by the given ID. <br />
     * Note: if not only the local part but also the domain is provided, make sure that is exists in Maileon.
     * 
     * @param integer $mailingId the ID of the mailing
     * @param string $email
     *  the ID of the target group to set
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setSender($mailingId, $email)
    {
        return $this->post('mailings/' . $mailingId . '/contents/sender', "<sender><![CDATA[" . $email . "]]></sender>");
    }
    
    /**
     * Fetches the sender email address of the mailing identified by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call, with the sender email address of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getSender($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/sender');
    }

    /**
     * Fetches the state of the mailing identified by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *  the result object of the API call, with the state of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getState($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/state');
    }
    
    /**
     * Updates the subject of the mailing referenced by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @param string $subject
     *  the subject of the mailing to set
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setSubject($mailingId, $subject)
    {
        return $this->post('mailings/' . $mailingId . '/contents/subject', "<subject><![CDATA[" . $subject . "]]></subject>");
    }
    
    /**
     * Fetches the subject of the mailing identified by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call, with the subject of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getSubject($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/subject');
    }
    
    /**
     * Updates the preview text of the mailing referenced by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @param string $previewText
     *  the preview text of the mailing to set, limit: 255 characters
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setPreviewText($mailingId, $previewText)
    {
        return $this->post(
            'mailings/' . $mailingId . '/contents/previewtext',
            "<previewtext><![CDATA[" . $previewText . "]]></previewtext>"
        );
    }
    
    /**
     * Fetches the preview text of the mailing identified by the given ID.
     * 
     * @param integer $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call, with the preview text of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getPreviewText($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/previewtext');
    }
    
    /**
     * Sets the template for a mailing. Be careful, all HTML/text contents will be resettet.
     * For templates from the same account, relative paths can be used in the form
     * "my template" of with sub folders "someSubFolder/my template".
     * For shared templates, an absolute path is required. The easiest way to find the
     * correct path is to set the template
     * manually and use getTemplate() to retrieve the name.
     * 
     * @param integer $mailingId the ID of the mailing
     * @param string $template
     *  the template id of the mailing to set
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setTemplate($mailingId, $template)
    {
        return $this->put('mailings/' . $mailingId . '/template', "<templateId><![CDATA[" . $template . "]]></templateId>");
    }
    
    /**
     * Returns the template of the mailing with the provided id.
     * For templates from the same account, relative paths will be returned
     * in the form "my template" of with sub folders "someSubFolder/my template".
     * For shared templates, an absolute path is returned.
     *
     * 
     * 
     * @param integer $mailingId the ID of the mailing
     *  the ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call, with the the corresponding template id of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getTemplate($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/template');
    }
    
    /**
     * Resets the HTML/text contents of the mailing to its template state.
     *
     * @param string $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function resetContentsToTemplate($mailingId)
    {
        return $this->put('mailings/' . $mailingId . '/contents/reset');
    }
    
    /**
     * Updates the senderalias of the mailing referenced by the given ID. <br />
     *
     * @param string $mailingId the ID of the mailing
     * @param string $senderalias
     *  the sender alias to set
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setSenderAlias($mailingId, $senderalias)
    {
        return $this->post(
            'mailings/' . $mailingId . '/contents/senderalias',
            "<senderalias><![CDATA[" . $senderalias . "]]></senderalias>"
        );
    }
    
    /**
     * Fetches the sender alias of the mailing identified by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *  the result object of the API call, with the sender alias of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getSenderAlias($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/senderalias');
    }
    
    /**
     * Updates the recipientalias of the mailing referenced by the given ID. <br />
     *
     * @param string $mailingId the ID of the mailing
     * @param string $recipientalias
     *  the recipient alias to set
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setRecipientAlias($mailingId, $recipientalias)
    {
        return $this->post(
            'mailings/' . $mailingId . '/contents/recipientalias',
            "<recipientalias><![CDATA[" . $recipientalias . "]]></recipientalias>"
        );
    }
    
    /**
     * Fetches the reply-to address of the mailing identified by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call, with the reply-to address of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getReplyToAddress($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/settings/replyto');
    }
    
    /**
     * Sets the reply-to address of the mailing identified by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     * @param bool $auto (default = true)
     *  If true, the Maileon autorecognition will be used and emails will be saved within Maileon.
     *  If false, a custom email address can be passed which gets all mails forwarded.
     * @param string $customEmail (default = empty)
     *  If $auto is false, this email will be used for manual responses.
     * @return
     * @throws MaileonAPIException
     */
    public function setReplyToAddress($mailingId, $auto = true, $customEmail = null)
    {
        $queryParameters = array(
            'auto' => ($auto == true) ? "true" : "false",
            'customEmail' => $customEmail
        );
        
        return $this->post('mailings/' . $mailingId . '/settings/replyto', null, $queryParameters);
    }
    
    /**
     *
     * Method to retrieve mailingy by scheduling time
     *
     * @param string $scheduleTime
     *  This is a date and time string that defines the filter for a mailing.
     *  The mailings before and after that time can be queried, see beforeSchedulingTime.
     *  The format is the standard SQL date: yyyy-MM-dd HH:mm:ss
     * @param bool $beforeSchedulingTime (default = true)
     *  If true, the mailings before the given time will be returned, if false,
     *  the mailings at or after the given time will be returned.
     * @param string[] fields (default = empty)
     *  This list contains the fields that shall be returned with the result.
     *  If this list is empty, only the IDs will be returned. Valid fields are: state, type, name, and scheduleTime
     * @param number page_index (default = 1)
     *  The index of the result page. The index must be greater or equal to 1.
     * @param number page_size (default = 100)
     *  The maximum count of items in the result page. If provided, the value of page_size must
     *  be in the range 1 to 1000.
     * @param string orderBy (default = id)
     *  The field to order results by
     * @param string order (default = DESC)
     *  The order
     * @return
     * @throws MaileonAPIException
     */
    public function getMailingsBySchedulingTime(
        $scheduleTime,
        $beforeSchedulingTime = true,
        $fields = array(),
        $page_index = 1,
        $page_size = 100,
        $orderBy = "id",
        $order = "DESC"
    ) {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size,
            'scheduleTime' => urlencode($scheduleTime),
            'beforeSchedulingTime' => ($beforeSchedulingTime == true) ? "true" : "false",
            'orderBy' => $orderBy,
            'order' => $order
        );
        
        $queryParameters = $this->appendArrayFields($queryParameters, "fields", $fields);
        
        return $this->get('mailings/filter/scheduletime', $queryParameters);
    }
    
    
    
    /**
     *
     * Method to retrieve mailingy by keywords
     *
     * @param string[] $keywords
     *  This is the list of keywords to filter for
     * @param string[] fields (default = empty)
     *  This list contains the fields that shall be returned with the result.
     *  If this list is empty, only the IDs will be returned. Valid fields are: state, type, name, and scheduleTime
     * @param number page_index (default = 1)
     *  The index of the result page. The index must be greater or equal to 1.
     * @param number page_size (default = 100)
     *  The maximum count of items in the result page. If provided, the value of page_size must
     *  be in the range 1 to 1000.
     * @return
     * @throws MaileonAPIException
     */
    public function getMailingsByKeywords($keywords, $keywordsOp = "and", $fields = array(), $page_index = 1, $page_size = 100)
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size,
            'order' => "DESC"
        );
        
        $queryParameters = $this->appendArrayFields($queryParameters, "keywords", $keywords);
        $queryParameters = $this->appendArrayFields($queryParameters, "keywordsOp", $keywordsOp);
        $queryParameters = $this->appendArrayFields($queryParameters, "fields", $fields);
        
        return $this->get('mailings/filter/keywords', $queryParameters);
    }
    
    /**
     *
     * Method to retrieve mailingy by types
     *
     * Types can be selected from 'doi','trigger', 'trigger_template' or 'regular' <br />
     * <br />
     * @see MailingFields
     *
     * @param string[] $types
     *  This is the list of types to filter for
     * @param string[] fields (default = empty)
     *  This list contains the fields that shall be returned with the result.
     *  If this list is empty, only the IDs will be returned. Valid fields are: state, type, name, and scheduleTime
     * @param number page_index (default = 1)
     *  The index of the result page. The index must be greater or equal to 1.
     * @param number page_size (default = 100)
     *  The maximum count of items in the result page. If provided, the value of page_size must
     *  be in the range 1 to 1000.
     * @return
     * @throws MaileonAPIException
     */
    public function getMailingsByTypes($types, $fields = array(), $page_index = 1, $page_size = 100)
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size,
            'order' => "DESC"
        );
        
        $queryParameters = $this->appendArrayFields($queryParameters, "types", $types);
        $queryParameters = $this->appendArrayFields($queryParameters, "fields", $fields);
        
        return $this->get('mailings/filter/types', $queryParameters);
    }
    
    /**
     * Method to retrieve mailings by states
     *
     * States can be selected from 'draft', 'failed', 'queued', 'checks', 'blacklist', 'preparing', 'sending',
     * 'canceled', 'paused', 'done', 'archiving', 'archived', 'released', and 'scheduled' (=waiting to be sent) <br />
     * <br />
     * @see MailingFields
     *
     * @param string[] states
     *  This is the list of states to filter for
     * @param string[] fields (default = empty)
     *  This list contains the fields that shall be returned with the result.
     *  If this list is empty, only the IDs will be returned. Valid fields are: state, type, name, and scheduleTime
     * @param number page_index (default = 1)
     *  The index of the result page. The index must be greater or equal to 1.
     * @param number page_size (default = 100)
     *  The maximum count of items in the result page. If provided, the value of page_size must
     *  be in the range 1 to 1000.
     * @return
     * @throws MaileonAPIException
     */
    public function getMailingsByStates($states, $fields = array(), $page_index = 1, $page_size = 100)
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size,
            'order' => "DESC"
        );
        
        $queryParameters = $this->appendArrayFields($queryParameters, "states", $states);
        $queryParameters = $this->appendArrayFields($queryParameters, "fields", $fields);
        
        return $this->get('mailings/filter/states', $queryParameters);
    }
    
    /**
     * Schedules the mailing to be instantly sent
     *
     * @param number mailingId
     *  The ID of the mailing to send now
     * @return
     * @throws MaileonAPIException
     */
    public function sendMailingNow($mailingId)
    {
        return $this->post('mailings/' . $mailingId . '/sendnow');
    }
    
    /**
     * Schedules the mailing for a given time. If <code>dispatchOption</code> is set, the enhanced scheduling options are enabled.
     *
     * @param number mailingId
     *  The ID of the mailing to schedule
     * @param date date
     *  The SQL conform date of the schedule day in the format YYYY-MM-DD
     * @param hours hours
     *  The schedule hour in the format of HH, 24 hours format
     * @param minute minute
     *  The schedule minutes in the format MM
     * @param string dispatchOption
     *  The time distribution strategy to choose from {'hour', 'weekdayhour', 'uniform'}.
     * @param dispatchEndInHours Number of hours begining from the dispatch start util which the dispatch distribution over the time has to be finished. Used in case of 'hour'
     *  dispatch option and 'uniform' option. Allowed values for the 'uniform' distribution are in [2..96], whereas for 'hour' strategy thery are ranging from [2..24].
     * @param dispatchEndInDays Number of days begining from the dispatch start util which the dispatch distribution over the time has to be finished. Used only with dispatch
     *  option 'weekdayhour' and its acceptable range is [1..7].
     * @param dispatchEndExactDatetime The exact end date util which the dispatch time distribution has to be finished. It is used when none of the arguments above
     *  <code>dispatchEndInHours</code>, <code>dispatchEndInDays</code> aren't set i.e. equals 0. Note that one of <code>dispatchEndInHours</code>, <code>dispatchEndInDays</code>,
     *  <code>dispatchEndExactDatetime</code> argument should be used in the request according to the selected dispatch option. Format: yyyy-MM-dd HH:mm
     * @param boolean clicksAsResponseReference
     *  The parameter determines the inclusion/exclusion of clicks as a response criteria when selecting {'hour', 'weekdayhour'} options.
     * @param int dispatchWavesGroup
     *  The number determines how many consecutive sending waves will be grouped when using {'hour', 'weekdayhour'} distribution. Supported values are {1, 2, 3 (default)}.
     * @param string dispatchUniformInterval 
     *  The arguments controls the interval {'hour', '30m', '20m', '15m', '10m'} for the 'uniform' strategy indicating the frequency of mailing
     *  distribution over time. It should equals null for {'hour', 'weekdayhour'} dispatch options.
     * @param string allowedHours 
     *  The value represents the allowed hours. Comma separated values for the allowed hours and can be combined with a range of hours. The required format looks
     *  like 0,3,5,17-21 as an example. The acceptable values rane is 0..23. Note that the if this argument is not provided, all 24H of the day will be considered as acceptable
     *  dispatch hours.
     * @return MaileonAPIResult
     * @throws MaileonAPIException
     */
    public function setMailingSchedule($mailingId, $date, $hours, $minutes, $dispatchOption = null, $dispatchEndInHours = null, $dispatchEndInDays = null, $dispatchEndExactDatetime = null, $clicksAsResponseReference = null, $dispatchWavesGroup = null, $dispatchUniformInterval = null, $allowedHours = null)
    {
        $queryParameters = array(
            'date' => $date,
            'hours' => $hours,
            'minutes' => $minutes
        );
        
        if (!empty($dispatchOption)) { $queryParameters ['dispatchOption'] = urlencode($dispatchOption); }
        if (!empty($dispatchEndInHours)) { $queryParameters ['dispatchEndInHours'] = urlencode($dispatchEndInHours); }
        if (!empty($dispatchEndInDays)) { $queryParameters ['dispatchEndInDays'] = urlencode($dispatchEndInDays); }
        if (!empty($dispatchEndExactDatetime)) { $queryParameters ['dispatchEndExactDatetime'] = urlencode($dispatchEndExactDatetime); }
        if (!empty($clicksAsResponseReference)) { $queryParameters ['clicksAsResponseReference'] = (boolval($clicksAsResponseReference) === true) ? "true" : "false"; }
        if (!empty($dispatchWavesGroup)) { $queryParameters ['dispatchWavesGroup'] = urlencode($dispatchWavesGroup); }
        if (!empty($dispatchUniformInterval)) { $queryParameters ['dispatchUniformInterval'] = urlencode($dispatchUniformInterval); }
        if (!empty($allowedHours)) { $queryParameters ['allowedHours'] = urlencode($allowedHours); }
        
        return $this->put('mailings/' . $mailingId . '/schedule', "", $queryParameters);
    }

    /**
     * Delete the schedule for the given mailing
     *
     * @param number mailingId
     * @return
     * @throws MaileonAPIException
     */
    public function deleteMailingSchedule($mailingId)
    {
        return $this->delete('mailings/' . $mailingId . '/schedule');
    }
    
    /**
     * Get the schedule for the given mailing
     *
     * @param number mailingId
     * @return
     * @throws MaileonAPIException
     */
    public function getMailingSchedule($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/schedule');
    }
    
    /**
     * Update the schedule for the given mailing. If <code>dispatchOption</code> is set, the enhanced scheduling options are enabled.
     *
     * @param number mailingId
     * @param date date
     *  The SQL conform date of the schedule day in the format YYYY-MM-DD
     * @param hours hours
     *  The schedule hour in the format of HH, 24 hours format
     * @param minute minute
     *  The schedule minutes in the format MM
     * @param string dispatchOption
     *  The time distribution strategy to choose from {'hour', 'weekdayhour', 'uniform'}.
     * @param dispatchEndInHours Number of hours begining from the dispatch start util which the dispatch distribution over the time has to be finished. Used in case of 'hour'
     *  dispatch option and 'uniform' option. Allowed values for the 'uniform' distribution are in [2..96], whereas for 'hour' strategy thery are ranging from [2..24].
     * @param dispatchEndInDays Number of days begining from the dispatch start util which the dispatch distribution over the time has to be finished. Used only with dispatch
     *  option 'weekdayhour' and its acceptable range is [1..7].
     * @param dispatchEndExactDatetime The exact end date util which the dispatch time distribution has to be finished. It is used when none of the arguments above
     *  <code>dispatchEndInHours</code>, <code>dispatchEndInDays</code> aren't set i.e. equals 0. Note that one of <code>dispatchEndInHours</code>, <code>dispatchEndInDays</code>,
     *  <code>dispatchEndExactDatetime</code> argument should be used in the request according to the selected dispatch option. Format: yyyy-MM-dd HH:mm
     * @param boolean clicksAsResponseReference
     *  The parameter determines the inclusion/exclusion of clicks as a response criteria when selecting {'hour', 'weekdayhour'} options.
     * @param int dispatchWavesGroup
     *  The number determines how many consecutive sending waves will be grouped when using {'hour', 'weekdayhour'} distribution. Supported values are {1, 2, 3 (default)}.
     * @param string dispatchUniformInterval 
     *  The arguments controls the interval {'hour', '30m', '20m', '15m', '10m'} for the 'uniform' strategy indicating the frequency of mailing
     *  distribution over time. It should equals null for {'hour', 'weekdayhour'} dispatch options.
     * @param string allowedHours 
     *  The value represents the allowed hours. Comma separated values for the allowed hours and can be combined with a range of hours. The required format looks
     *  like 0,3,5,17-21 as an example. The acceptable values rane is 0..23. Note that the if this argument is not provided, all 24H of the day will be considered as acceptable
     *  dispatch hours.
     * @return MaileonAPIResult
     * @throws MaileonAPIException
     */
    public function updateMailingSchedule($mailingId, $date, $hours, $minutes, $dispatchOption = null, $dispatchEndInHours = null, $dispatchEndInDays = null, $dispatchEndExactDatetime = null, $clicksAsResponseReference = null, $dispatchWavesGroup = null, $dispatchUniformInterval = null, $allowedHours = null)
    {
        $queryParameters = array(
            'date' => $date,
            'hours' => $hours,
            'minutes' => $minutes
        );
        
        if (!empty($dispatchOption)) { $queryParameters ['dispatchOption'] = urlencode($dispatchOption); }
        if (!empty($dispatchEndInHours)) { $queryParameters ['dispatchEndInHours'] = urlencode($dispatchEndInHours); }
        if (!empty($dispatchEndInDays)) { $queryParameters ['dispatchEndInDays'] = urlencode($dispatchEndInDays); }
        if (!empty($dispatchEndExactDatetime)) { $queryParameters ['dispatchEndExactDatetime'] = urlencode($dispatchEndExactDatetime); }
        if (!empty($clicksAsResponseReference)) { $queryParameters ['clicksAsResponseReference'] = (boolval($clicksAsResponseReference) === true) ? "true" : "false"; }
        if (!empty($dispatchWavesGroup)) { $queryParameters ['dispatchWavesGroup'] = urlencode($dispatchWavesGroup); }
        if (!empty($dispatchUniformInterval)) { $queryParameters ['dispatchUniformInterval'] = urlencode($dispatchUniformInterval); }
        if (!empty($allowedHours)) { $queryParameters ['allowedHours'] = urlencode($allowedHours); }
        
        return $this->post('mailings/' . $mailingId . '/schedule', "", $queryParameters);
    }
    
    
    /**
     * Fetches the DOI mailing key of the mailing identified by the given ID.
     *
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *    the result object of the API call, with the target group id of the mailing
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getDoiMailingKey($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/settings/doi_key', null, "text/html");
    }
    
    /**
     * Sets the key of the DOI mailing identified by the given ID.
     *
     * @param integer $mailingId The ID of the mailing
     * @param string $doiKey
     *  The new DOI key.
     * @return
     * @throws MaileonAPIException
     */
    public function setDoiMailingKey($mailingId, $doiKey)
    {
        return $this->post('mailings/' . $mailingId . '/settings/doi_key', "<doi_key>$doiKey</doi_key>");
    }
    
    /**
     * Deactivates a trigger mailing by ID.
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function deactivateTriggerMailing($mailingId)
    {
        return $this->delete("mailings/${mailingId}/dispatching");
    }
    
    
    /**
     * Get the dispatch data for a trigger mailing by mailing ID.
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function getTriggerDispatchLogic($mailingId)
    {
        return $this->get("mailings/${mailingId}/dispatching");
    }
    
    
    /**
     * Get the schedule for regular mailings by mailing ID.
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function getSchedule($mailingId)
    {
        return $this->get("mailings/${mailingId}/schedule");
    }
    
    /**
     * Get the archive url for the mailing ID.
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function getArchiveUrl($mailingId)
    {
        return $this->get("mailings/${mailingId}/archiveurl");
    }
    
    /**
     * Get the report url for the mailing ID.
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function getReportUrl($mailingId)
    {
        return $this->get("mailings/${mailingId}/reporturl");
    }
    
    /**
     * Updates the name of the mailing referenced by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     * @param string $name
     *  the name of the mailing to set
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setName($mailingId, $name)
    {
        return $this->post('mailings/' . $mailingId . '/name', "<name><![CDATA[" . $name . "]]></name>");
    }
    
    /**
     * Get the name for the mailing by mailing ID.
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function getName($mailingId)
    {
        return $this->get("mailings/${mailingId}/name");
    }
    
    /**
     * Updates the tags of the mailing referenced by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     * @param array $tags
     *  the tags
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setTags($mailingId, $tags)
    {
        return $this->post('mailings/' . $mailingId . '/settings/tags', "<tags><![CDATA[" . join("#", $tags) . "]]></tags>");
    }
    
    /**
     * Get the tags for the mailing identified by mailing ID.
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function getTags($mailingId)
    {
        return $this->get("mailings/${mailingId}/settings/tags");
    }
    
    /**
     * Updates the locale of the mailing referenced by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     * @param string locale
     *  the locale in the form xx: e.g. de, en, fr, �
     * @return MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function setLocale($mailingId, $locale)
    {
        return $this->post('mailings/' . $mailingId . '/settings/locale', "<locale>$locale</locale>");
    }
    
    /**
     * Get the locale for the mailing identified by mailing ID in the form xx: e.g. de, en, fr, �
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function getLocale($mailingId)
    {
        return $this->get("mailings/${mailingId}/settings/locale");
    }
    
    /**
     * Execute the RSS SmartMailing functionality for mailings, i.e. fill all SmartMailing
     * Tags from the described RSS-Feeds.
     * For more information about RSS-SmartMailing, please check our customer support at service@xqueue.com
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function fillRssSmartContentTags($mailingId)
    {
        return $this->post("mailings/${mailingId}/contents/smartmailing/rss");
    }
    
    /**
     * Copy the mailing with the given mailing ID.
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     *  the result of the operation
     */
    public function copyMailing($mailingId)
    {
        return $this->post("mailings/${mailingId}/copy");
    }
    
    /**
     * Read a binary file from the file system and adds it as an attachment to this transaction.
     *
     * @param integer $mailingId The ID of the mailing
     * @param string $filename
     * @param string $contentType
     * @param string $attachmentFileName Name of the file in the attachments
     *
     * @return MaileonAPIResult
     * @throws MaileonAPIException
     */
    public function addAttachmentFromFile($mailingId, $filename, $contentType, $attachmentFileName = null)
    {
        $handle = fopen($filename, "rb");
        if (false === $filename) {
            throw new MaileonAPIException("Cannot read file " . $filename . ".");
        }
        $contents = '';
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        if ($attachmentFileName === null) {
            $attachmentFileName = basename($filename);
        }
        
        return $this->addAttachment($mailingId, $attachmentFileName, $contentType, $contents);
    }
    
    /**
     * Adds an attachment to the mailing with the provided id.
     *
     * @param integer $mailingId The mailing id
     * @param string $filename Filename of the attachment to be displayed in sent emails.
     *  It is recommended to keep the filename short and to use an extension corresponding
     *  to the mime type of the attachment.
     * @param string $contentType The mime type of the attachment
     * @param string $contents The file content
     *
     * @return MaileonAPIResult
     */
    public function addAttachment($mailingId, $filename, $contentType, $contents)
    {
        $queryParameters = array('filename' => urlencode($filename));
        
        return $this->post(
            "mailings/${mailingId}/attachments",
            $contents,
            $queryParameters,
            null,
            null,
            $contentType,
            strlen($contents)
        );
    }
    
    /**
     * Returns a list of the registered attachments for the mailing with the provided id.
     *
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     */
    public function getAttachments($mailingId)
    {
        return $this->get("mailings/${mailingId}/attachments");
    }
    
    /**
     * Returns the attachment with the provided id as a file.
     *
     * @param integer $mailingId The ID of the mailing
     * @param integer $attachmentId
     * @return MaileonAPIResult
     */
    public function getAttachment($mailingId, $attachmentId)
    {
        return $this->get("mailings/${mailingId}/attachments/${attachmentId}");
    }
    
    /**
     * Returns the count of available attachments in the mailing with the provided id.
     *
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     */
    public function getAttachmentsCount($mailingId)
    {
        return $this->get("mailings/${mailingId}/attachments/count");
    }
    
    /**
     * Deletes all the attachments that belong to the mailing with the provided id. The mailing should not be sealed.
     *
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     */
    public function deleteAttachments($mailingId)
    {
        return $this->delete("mailings/${mailingId}/attachments/");
    }
    
    /**
     * Deletes the attachment with the provided id from the mailing. The mailing should not be sealed.
     *
     * @param integer $mailingId The ID of the mailing
     * @param integer $attachmentId
     *
     * @return MaileonAPIResult
     * @throws MaileonAPIException
     */
    public function deleteAttachment($mailingId, $attachmentId)
    {
        if (empty($attachmentId)) {
            throw new MaileonAPIException("no attachment id specified");
        }
        
        return $this->delete("mailings/${mailingId}/attachments/${attachmentId}");
    }
    
    /**
     * Copies the attachments of a source mailing into a target mailing. Note that the target
     *  mailing should not be sealed and that the resulting total count of attachments in the
     *  target mailing should not exceed 10.
     *
     * @param integer $mailingId The ID of the mailing
     * @param integer $srcMailingId
     * @return MaileonAPIResult
     */
    public function copyAttachments($mailingId, $srcMailingId)
    {
        $queryParameters = array('src_mailing_id' => $srcMailingId);
        
        return $this->put("mailings/${mailingId}/attachments", "", $queryParameters);
    }
    
    /**
     * Returns a list of custom properties for the mailing with the provided id.
     *
     * @param integer $mailingId The ID of the mailing
     * @return MaileonAPIResult
     */
    public function getCustomProperties($mailingId)
    {
        return $this->get("mailings/${mailingId}/settings/properties");
    }
    
    
    /**
     * Adds a list of custom properties to the mailing with the provided id.
     *
     * @param integer $mailingId The ID of the mailing
     * @param array $properties Array of CustomProperty or single property
     * @return MaileonAPIResult
     */
    public function addCustomProperties($mailingId, $properties)
    {
        
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><properties></properties>");
        
        if (is_array($properties)) {
            foreach ($properties as $property) {
                $this->sxmlAppend($xml, $property->toXML());
            }
        } else {
            $this->sxmlAppend($xml, $properties->toXML());
        }
        
        return $this->post("mailings/${mailingId}/settings/properties", $xml->asXML());
    }
    
    
    /**
     * Updates a custom property of the mailing with the provided id.
     *
     * @param integer $mailingId The ID of the mailing
     * @param CustomProperty $property
     * @return MaileonAPIResult
     */
    public function updateCustomProperty($mailingId, $property)
    {
        
        $queryParameters = array(
            'name' => $property->key,
            'value' => $property->value
        );
        
        return $this->put("mailings/${mailingId}/settings/properties", "", $queryParameters);
    }
    
    
    /**
     * Deletes a custom property of the mailing with the provided id.
     *
     * @param integer $mailingId The ID of the mailing
     * @param string $propertyName The name of the property to delete
     * @return MaileonAPIResult
     */
    public function deleteCustomProperty($mailingId, $propertyName)
    {
        $queryParameters = array(
            'name' => $propertyName,
        );
        
        return $this->delete("mailings/${mailingId}/settings/properties", $queryParameters);
    }
    
    
    /**
     * Sends a testmail for the mailing with the provided id to a given email address.
     * If the email address does not exist within your contacts,
     * the personalization is done according to your default personalization user configured in Maileon.
     * <p><a href="http://dev.maileon.com/mailing-send-testmail-to-single-emailaddress">Documentation website</a></p>
     *
     * @param mailingId id of existing mailing
     * @param email email address
     * @throws MaileonAPIException
     */
    public function sendTestMail($mailingId, $email)
    {
        $queryParameters = array(
            'email' => $email
        );
        
        return $this->post("mailings/${mailingId}/sendtestemail", "", $queryParameters);
    }
    
    /**
     * Sends a testmail for the mailing with the provided id to a test-targetgroup givenby its ID.
     *
     * @param mailingId
     * @param testTargetGroupId
     * @throws MaileonAPIException
     */
    public function sendTestMailToTestTargetGroup($mailingId, $testTargetGroupId)
    {
        $queryParameters = array(
            'test_targetgroup_id' => $testTargetGroupId
        );
        
        return $this->post("mailings/${mailingId}/checks/testsendout", "", $queryParameters);
    }
    
    /**
     * Assigns a mailing blacklist to a mailing.
     *
     * @param mailingId id of the existing mailing
     * @param mailingBlacklistId id of the mailing blacklist to be assigned to the mailing
     * @throws MaileonAPIException
     */
    public function addMailingBlacklist($mailingId, $mailingBlacklistId)
    {
        return $this->post("mailings/${mailingId}/mailingblacklists/${mailingBlacklistId}");
    }
    
    /**
     * Deletes a mailing blacklist from a mailing.
     *
     * @param mailingId
     * @param mailingBlacklistId
     * @throws MaileonAPIException
     */
    public function deleteMailingBlacklist($mailingId, $mailingBlacklistId)
    {
        return $this->delete("mailings/${mailingId}/mailingblacklists/${mailingBlacklistId}");
    }
    
    
    
    /**
     * Retrieve the domain of this mailing
     *
     * @param mailingId
     * @throws MaileonAPIException
     */
    public function getMailingDomain($mailingId)
    {
        return $this->get("mailings/${mailingId}/domain/");
    }
    
    /**
     * Retrieve all blacklists assigned to this mailing
     *
     * @param mailingId
     * @throws MaileonAPIException
     */
    public function getMailingBlacklists($mailingId)
    {
        return $this->get("mailings/${mailingId}/mailingblacklists/");
    }
    
    public function sxmlAppend(\SimpleXMLElement $to, \SimpleXMLElement $from)
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
    
    /**
     * Get the configured recipient alias for the given mailing
     *
     * @param mailingId
     * @throws MaileonAPIException
     */
    public function getRecipientAlias($mailingId)
    {
        return $this->get("mailings/${mailingId}/contents/recipientalias");
    }

    /**
     * Get the tracking strategy for the given mailing
     *
     * @param number mailingId
     * @return
     * @throws MaileonAPIException
     */
    public function getTrackingStrategy($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/settings/trackingstrategy');
    }
    
    /**
     * Get the configured speed level for the given mailing
     *
     * @param mailingId
     * @throws MaileonAPIException
     */
    public function getSpeedLevel($mailingId)
    {
        return $this->get("mailings/${mailingId}/settings/speedlevel");
    }
    
    /**
     * Get the configured post sendout cleanup state for the given mailing
     *
     * @param mailingId
     * @throws MaileonAPIException
     */
    public function getPostSendoutCleanupState($mailingId)
    {
        return $this->get("mailings/${mailingId}/settings/post_sendout_cleanup");
    }
}
