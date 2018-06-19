<?php

namespace XQueue\Maileon\API\Mailings;

use XQueue\Maileon\API\AbstractMaileonService;
use XQueue\Maileon\API\MaileonAPIException;

/**
 * Facade that wraps the REST service for mailings.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
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
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function createMailing($name, $subject, $deprecatedParameter = false, $type = "regular")
    {
        $queryParameters = array(
            'name' => urlencode($name),
            'subject' => urlencode($subject),
            'type' => urlencode($type),
        );

        return $this->post('mailings', "", $queryParameters);
    }

    /**
     * Get the ID of a mailing by its name
     */
    function getMailingIdByName($mailingName)
    {
        return $this->get('mailings/name/' . urlencode($mailingName));
    }

    /**
     * Check if a mailing with the given name exists and return true or false
     */
    function checkIfMailingExistsByName($mailingName)
    {
        $response = $this->get('mailings/name/' . urlencode($mailingName));
        return ($response->isSuccess());
    }

    /**
     * Disable all QoS checks for a given mailing
     */
    function disableQosChecks($mailingId)
    {
        return $this->put('mailings/' . $mailingId . '/settings/disableQosChecks');
    }

    /**
     * Sets the dispatch logic for trigger mailings
     */
    function setTriggerDispatchLogic($mailingId, $logic)
    {
        $queryParameters = array();
        return $this->put('mailings/' . $mailingId . '/dispatching', $logic, $queryParameters);
    }

    /**
     * Used for DOI Mailings
     * */
    function setTriggerActive($mailingId)
    {
        return $this->post('mailings/' . $mailingId . '/dispatching/activate', "");
    }

    /**
     * Deletes an active trigger mailing.
     * @param integer $id
     *  the ID of the mailing to delete
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function deleteActiveTriggerMailing($mailingId)
    {
        return $this->delete("mailings/".$mailingId."/dispatching");
    }

    /**
     * Deletes a mailing by ID.
     * @param integer $id
     *  the ID of the mailing to delete
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function deleteMailing($id)
    {
        return $this->delete("mailings/".$id);
    }

    /**
     * Updates the HTML content of the mailing referenced by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param string $html
     *  the new HTML content of the mailing
     * @param bool $doImageGrabbing
     *  specifies if image grabbing should be performed
     * @param bool $doLinkTracking
     *  specifies if link tracking should be performed
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setHTMLContent($mailingId, $html, $doImageGrabbing = true, $doLinkTracking = false)
    {
        $queryParameters = array(
            'doImageGrabbing' => ($doImageGrabbing == TRUE) ? "true" : "false",
            'doLinkTracking' => ($doLinkTracking == TRUE) ? "true" : "false"
        );
        return $this->post('mailings/' . $mailingId . '/contents/html', $html, $queryParameters, "text/html");
    }

    /**
     * Updates the TEXT content of the mailing referenced by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param string $text
     *  the new TEXT content of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setTextContent($mailingId, $text)
    {
        return $this->post('mailings/' . $mailingId . '/contents/text', $text, array(), "text/plain");
    }

    /**
     * Fetches the HTML content of the mailing identified by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the HTML content string of the mailing
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function getHTMLContent($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/html', null, "text/html");
    }

    /**
     * Fetches the TEXT content of the mailing identified by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the TEXT content string of the mailing
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function getTextContent($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/text', null, "text/plain");
    }

    /**
     * Updates the target group id of the mailing referenced by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param string $targetGroupId
     *  the ID of the target group to set
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setTargetGroupId($mailingId, $targetGroupId)
    {
        return $this->post('mailings/' . $mailingId . '/targetgroupid', "<targetgroupid>" . $targetGroupId . "</targetgroupid>");
    }

    /**
     * Fetches the target group id of the mailing identified by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the target group id of the mailing
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function getTargetGroupId($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/targetgroupid', null);
    }

    /**
     * Updates the sender email address of the mailing referenced by the given ID. <br />
     * Note: if not only the local part but also the domain is provided, make sure that is exists in Maileon.
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param string $email
     *  the ID of the target group to set
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setSender($mailingId, $email)
    {
        return $this->post('mailings/' . $mailingId . '/contents/sender', "<sender>" . $email . "</sender>");
    }

    /**
     * Fetches the sender email address of the mailing identified by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the sender email address of the mailing
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function getSender($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/sender');
    }

    /**
     * Updates the subject of the mailing referenced by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param string $subject
     *  the subject of the mailing to set
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setSubject($mailingId, $subject)
    {
        return $this->post('mailings/' . $mailingId . '/contents/subject', "<subject>" . $subject . "</subject>");
    }

    /**
     * Fetches the subject of the mailing identified by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the subject of the mailing
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function getSubject($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/contents/subject');
    }
    
    /**
     * Updates the senderalias of the mailing referenced by the given ID. <br />
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param string $senderalias
     *  the sender alias to set
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setSenderAlias($mailingId, $senderalias)
    {
        return $this->post('mailings/' . $mailingId . '/contents/senderalias', "<senderalias>" . $senderalias . "</senderalias>");
    }

    /**
     * Updates the recipientalias of the mailing referenced by the given ID. <br />
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param string $recipientalias
     *  the recipient alias to set
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setRecipientAlias($mailingId, $recipientalias)
    {
        return $this->post('mailings/' . $mailingId . '/contents/recipientalias', "<recipientalias>" . $recipientalias . "</recipientalias>");
    }

    /**
     * Fetches the reply-to address of the mailing identified by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the reply-to address of the mailing
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function getReplyToAddress($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/settings/replyto');
    }

    /**
     * Sets the reply-to address of the mailing identified by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing
     * @param bool $auto (default = true)
     *  If true, the Maileon autorecognition will be used and emails will be saved within Maileon. If false, a custom email address can be passed which gets all mails forwarded.
     * @param string $customEmail (default = empty)
     *  If $auto is false, this email will be used for manual responses.
     * @return
     * @throws MaileonException
     */
    function setReplyToAddress($mailingId, $auto = true, $customEmail = null)
    {
        $queryParameters = array(
            'auto' => ($auto == TRUE) ? "true" : "false",
            'customEmail' => $customEmail
        );

        return $this->post('mailings/' . $mailingId . '/settings/replyto', null, $queryParameters);
    }

    /**
     * Types can be selected from 'doi','trigger', 'trigger_template' or 'regular' <br />
     * <br />
     * @see MailingFields
     *
     * @param string $scheduleTime
     *  This is a date and time string that defines the filter for a mailing. The mailings before and after that time can be queried, see beforeSchedulingTime. The format is the standard SQL date: yyyy-MM-dd HH:mm:ss
     * @param bool $beforeSchedulingTime (default = true)
     *  If true, the mailings before the given time will be returned, if false, the mailings at or after the given time will be returned.
     * @param string[] fields (default = empty)
     *  This list contains the fields that shall be returned with the result. If this list is empty, only the IDs will be returned. Valid fields are: state, type, name, and scheduleTime
     * @param number page_index (default = 1)
     *  The index of the result page. The index must be greater or equal to 1.
     * @param number page_size (default = 100)
     *    The maximum count of items in the result page. If provided, the value of page_size must be in the range 1 to 1000.
     * @param string orderBy (default = id)
     *    The field to order results by
     * @param string order (default = DESC)
     *    The order
     * @return
     * @throws MaileonException
     */
    function getMailingsBySchedulingTime($scheduleTime, $beforeSchedulingTime = true, $fields = array(), $page_index = 1, $page_size = 100, $orderBy = "id", $order = "DESC")
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size,
            'scheduleTime' => urlencode($scheduleTime),
            'beforeSchedulingTime' => ($beforeSchedulingTime == TRUE) ? "true" : "false",
            'orderBy' => $orderBy,
            'order' => $order
        );

        $queryParameters = $this->appendArrayFields($queryParameters, "fields", $fields);

        return $this->get('mailings/filter/scheduletime', $queryParameters);
    }

    /**
     * Types can be selected from 'doi','trigger', 'trigger_template' or 'regular' <br />
     * <br />
     * @see MailingFields
     *
     * @param string[] $types
     *  This is the list of types to filter for
     * @param string[] fields (default = empty)
     *  This list contains the fields that shall be returned with the result. If this list is empty, only the IDs will be returned. Valid fields are: state, type, name, and scheduleTime
     * @param number page_index (default = 1)
     *  The index of the result page. The index must be greater or equal to 1.
     * @param number page_size (default = 100)
     *    The maximum count of items in the result page. If provided, the value of page_size must be in the range 1 to 1000.
     * @return
     * @throws MaileonException
     */
    function getMailingsByTypes($types, $fields = array(), $page_index = 1, $page_size = 100)
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
     * Schedules the mailing to be instantly sent
     *
     * @param number mailingId
     *  The ID of the mailing to send now
     * @return
     * @throws MaileonException
     */
    function sendMailingNow($mailingId)
    {
        return $this->post('mailings/' . $mailingId . '/sendnow');
    }



    /**
     * Fetches the DOI mailing key of the mailing identified by the given ID.
     *
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call, with the target group id of the mailing
     *  available at com_maileon_api_MaileonAPIResult::getResult()
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function getDoiMailingKey($mailingId)
    {
        return $this->get('mailings/' . $mailingId . '/settings/doi_key', null, "text/html");
    }

    /**
     * Sets the key of the DOI mailing identified by the given ID.
     *
     * @param number $mailingId
     *  the ID of the mailing
     * @param string $doiKey
     *  The new DOI key.
     * @return
     * @throws MaileonException
     */
    function setDoiMailingKey($mailingId, $doiKey)
    {
        return $this->post('mailings/' . $mailingId . '/settings/doi_key', "<doi_key>$doiKey</doi_key>");
    }

    /**
     * Deactivates a trigger mailing by ID.
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function deactivateTriggerMailing($mailingId)
    {
        return $this->delete("mailings/${mailingId}/dispatching");
    }



    /**
     * Get the dispatch data for a trigger mailing by mailing ID.
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function getTriggerDispatchLogic($mailingId)
    {
        return $this->get("mailings/${mailingId}/dispatching");
    }


    /**
     * Get the schedule for regular mailings by mailing ID.
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function getSchedule($mailingId)
    {
        return $this->get("mailings/${mailingId}/schedule");
    }

    /**
     * Get the archive url for the mailing ID.
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function getArchiveUrl($mailingId)
    {
        return $this->get("mailings/${mailingId}/archiveurl");
    }

    /**
     * Updates the name of the mailing referenced by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param string $name
     *  the name of the mailing to set
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setName($mailingId, $name)
    {
        return $this->post('mailings/' . $mailingId . '/name', "<name>" . $name . "</name>");
    }

    /**
     * Get the name for the mailing by mailing ID.
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function getName($mailingId)
    {
        return $this->get("mailings/${mailingId}/name");
    }

    /**
     * Updates the tags of the mailing referenced by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param array $tags
     *  the tags
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setTags($mailingId, $tags)
    {
        return $this->post('mailings/' . $mailingId . '/settings/tags', "<tags>" . join("#", $tags) . "</tags>");
    }

    /**
     * Get the tags for the mailing identified by mailing ID.
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function getTags($mailingId)
    {
        return $this->get("mailings/${mailingId}/settings/tags");
    }

    /**
     * Updates the locale of the mailing referenced by the given ID.
     *
     * @param string $mailingId
     *  the ID of the mailing to update
     * @param string locale
     *  the locale in the form xx: e.g. de, en, fr, �
     * @return \em com_maileon_api_MaileonAPIResult
     *    the result object of the API call
     * @throws com_maileon_api_MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    function setLocale($mailingId, $locale)
    {
        return $this->post('mailings/' . $mailingId . '/settings/locale', "<locale>$locale</locale>");
    }

    /**
     * Get the locale for the mailing identified by mailing ID in the form xx: e.g. de, en, fr, �
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function getLocale($mailingId)
    {
        return $this->get("mailings/${mailingId}/settings/locale");
    }

    /**
     * Execute the RSS SmartMailing functionality for mailings, i.e. fill all SmartMailing Tags from the described RSS-Feeds.
     * For more information about RSS-SmartMailing, please check our customer support at service@xqueue.com
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function fillRssSmartContentTags($mailingId)
    {
        return $this->post("mailings/${mailingId}/contents/smartmailing/rss");
    }

    /**
     * Copy the mailing with the given mailing ID.
     * @param number $mailingId
     *  the ID of the mailing
     * @return \em com_maileon_api_MaileonAPIResult
     *  the result of the operation
     */
    function copyMailing($mailingId)
    {
        return $this->post("mailings/${mailingId}/copy");
    }
    
    /**
     * Read a binary file from the file system and adds it as an attachment to this transaction.
     * 
     * @param type $mailingId
     * @param type $filename
     * @param type $contentType
     * @param type $attachmentFileName Name of the file in the attachments
     * 
     * @return com_maileon_api_MaileonAPIResult
     * @throws com_maileon_api_MaileonAPIException
     */
    function addAttachmentFromFile($mailingId, $filename, $contentType, $attachmentFileName = null) {
        $handle = fopen($filename, "rb");
        if (FALSE === $filename) {
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
     * @param int $mailingId The mailing id
     * @param string $filename Filename of the attachment to be displayed in sent emails. It is recommended to keep the filename short and to use an extension corresponding to the mime type of the attachment.
     * @param string $contentType The mime type of the attachment
     * @param string $contents The file content
     * 
     * @return com_maileon_api_MaileonAPIResult
     */
    function addAttachment($mailingId, $filename, $contentType, $contents) {
        $queryParameters = array( 'filename' => $filename );
        return $this->post("mailings/${mailingId}/attachments", $contents, $queryParameters, null, null, $contentType, strlen($contents));
    }
    
    /**
     * Returns a list of the registered attachments for the mailing with the provided id.
     * 
     * @param int $mailingId
     * @return com_maileon_api_MaileonAPIResult
     */
    function getAttachments($mailingId) {
        return $this->get("mailings/${mailingId}/attachments");
    }
    
    /**
     * Returns the attachment with the provided id as a file.
     * 
     * @param int $mailingId
     * @param int $attachmentId
     * @return com_maileon_api_MaileonAPIResult
     */
    function getAttachment($mailingId, $attachmentId) {
        return $this->get("mailings/${mailingId}/attachments/${attachmentId}");
    }
    
    /**
     * Returns the count of available attachments in the mailing with the provided id.
     * 
     * @param int $mailingId
     * @return com_maileon_api_MaileonAPIResult
     */
    function getAttachmentsCount($mailingId) {
        return $this->get("mailings/${mailingId}/attachments/count");
    }
    
    /**
     * Deletes all the attachments that belong to the mailing with the provided id. The mailing should not be sealed.
     * 
     * @param int $mailingId
     * @return type
     */
    function deleteAttachments($mailingId) {
        return $this->delete("mailings/${mailingId}/attachments/");
    }
    
    /**
     * Deletes the attachment with the provided id from the mailing. The mailing should not be sealed.
     * 
     * @param int $mailingId
     * @param int $attachmentId
     * 
     * @return com_maileon_api_MaileonAPIResult
     * @throws com_maileon_api_MaileonAPIException
     */
    function deleteAttachment($mailingId, $attachmentId) {
        if(empty($attachmentId)) { throw new MaileonAPIException("no attachment id specified"); }
        
        return $this->delete("mailings/${mailingId}/attachments/${attachmentId}");
    }
    
    /**
     * Copies the attachments of a source mailing into a target mailing. Note that the target mailing should not be sealed and that the resulting total count of attachments in the target mailing should not exceed 10.
     * 
     * @param int $mailingId
     * @param int $srcMailingId
     * @return com_maileon_api_MaileonAPIResult
     */
    function copyAttachments($mailingId, $srcMailingId) {
        $queryParameters = array( 'src_mailing_id' => $srcMailingId );
        
        return $this->put("mailings/${mailingId}/attachments", "", $queryParameters);
    }

    /**
     * Returns a list of custom properties for the mailing with the provided id.
     *
     * @param int $mailingId
     * @return com_maileon_api_MaileonAPIResult
     */
    function getCustomProperties($mailingId) {
        return $this->get("mailings/${mailingId}/settings/properties");
    }


    /**
     * Adds a list of custom properties to the mailing with the provided id.
     *
     * @param int $mailingId
     * @param array $properties Array of com_maileon_api_mailings_CustomProperty or single property
     * @return com_maileon_api_MaileonAPIResult
     */
    function addCustomProperties($mailingId, $properties) {

        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><properties></properties>");

        if (is_array($properties)) {
            foreach ($properties as $property) {
                $this->sxml_append($xml, $property->toXML());
            }
        } else {
            $this->sxml_append($xml, $properties->toXML());
        }

        return $this->post("mailings/${mailingId}/settings/properties", $xml->asXML());
    }


    /**
     * Updates a custom property of the mailing with the provided id.
     *
     * @param int $mailingId
     * @param com_maileon_api_mailings_CustomProperty $property
     * @return com_maileon_api_MaileonAPIResult
     */
    function updateCustomProperty($mailingId, $property) {

        $queryParameters = array(
            'name' => $property->key,
            'value' => $property->value
        );

        return $this->put("mailings/${mailingId}/settings/properties", "", $queryParameters);
    }


    /**
     * Deletes a custom property of the mailing with the provided id.
     *
     * @param int $mailingId
     * @param string $propertyName The name of the property to delete
     * @return com_maileon_api_MaileonAPIResult
     */
    function deleteCustomProperty($mailingId, $propertyName) {

        $queryParameters = array(
            'name' => $propertyName,
        );

        return $this->delete("mailings/${mailingId}/settings/properties", $queryParameters);
    }

    function sxml_append(\SimpleXMLElement $to, \SimpleXMLElement $from) {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }

}