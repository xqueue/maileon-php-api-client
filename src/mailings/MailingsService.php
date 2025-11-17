<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;
use SimpleXMLElement;

use function base64_encode;
use function basename;
use function dom_import_simplexml;
use function fclose;
use function feof;
use function fopen;
use function fread;
use function implode;
use function is_array;
use function json_encode;
use function mb_convert_encoding;
use function rawurlencode;
use function strlen;
use function urlencode;

/**
 * Facade that wraps the REST service for mailings.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 * @author Andreas Lange
 */
class MailingsService extends AbstractMaileonService
{
    /**
     * Creates a new mailing.
     *
     * @param string $name                The name of the mailing
     * @param string $subject             The subject of the mailing
     * @param bool   $deprecatedParameter this parameter was never used by the API
     * @param string $type                The type of the mailing, which can be one of 'doi', 'trigger', or 'regular'.
     * @param string $editorVersion       The version of the CMS to create the mailing for. Valid values for CMS1: 'v1', '1'. Valid values
     *                                    for CMS2: 'v2', '2'. By default (no value), the mailing will be created as a CMS2 template, if
     *                                    CMS2 is activated.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function createMailing(
        $name,
        $subject,
        $deprecatedParameter = false,
        $type = 'regular',
        $editorVersion = ''
    ) {
        $queryParameters = [
            'name'    => urlencode($name),
            'subject' => urlencode($subject),
            'type'    => urlencode($type),
        ];

        // As of deployment on 23.08.2021 empty strings are not accepted anymore. This will be changed to work again in
        // near future but to make the client work again, leave the variable if not set.
        if (! empty($editorVersion)) {
            $queryParameters['editorVersion'] = urlencode($editorVersion);
        }

        return $this->post(
            'mailings',
            '',
            $queryParameters
        );
    }

    /**
     * Get the ID of a mailing by its name
     *
     * @param string $mailingName
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getMailingIdByName($mailingName)
    {
        $encodedMailingName = rawurlencode(mb_convert_encoding((string) $mailingName, 'UTF-8'));

        return $this->get("mailings/name/$encodedMailingName");
    }

    /**
     * Get the type of mailing. It can be either 'doi', 'trigger', or 'regular'
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getType($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/type");
    }

    /**
     * Check if a mailing with the given name exists and return true or false
     *
     * @param $mailingName
     *
     * @return bool
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function checkIfMailingExistsByName($mailingName): bool
    {
        $encodedMailingName = rawurlencode(mb_convert_encoding((string) $mailingName, 'UTF-8'));

        return $this->get("mailings/name/$encodedMailingName")->isSuccess();
    }

    /**
     * Disable all QoS checks for a given mailing
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function disableQosChecks($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->put("mailings/$encodedMailingId/settings/disableQosChecks");
    }

    /**
     * Set ignoring permissions for sendouts.
     * This is only possible with transaction/trigger mails and be aware to NOT add advertisements
     * in mails you send without advertisement permission. This is meant for order confirmations and the like.
     *
     * @param int $mailingId
     * @param     $ignorePermission
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setIgnorePermission(
        $mailingId,
        $ignorePermission
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        if ($ignorePermission === true) {
            $ignorePermission = 'true';
        } elseif ($ignorePermission === false) {
            $ignorePermission = 'false';
        }

        return $this->post(
            "mailings/$encodedMailingId/settings/ignorepermission", "<ignore_permission>$ignorePermission</ignore_permission>"
        );
    }

    /**
     * Check if a (trigger) mail is set to ignore permission during sendout (order confirmation, invoices, ...)
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function isIgnorePermission($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/settings/ignorepermission");
    }

    /**
     * Set cleanup option for post sendout processing.
     * This flag defines if the used contact list and filter should be deleted after sendout.
     *
     * @param int     $mailingId the ID of the mailing
     * @param boolean $cleanup   can be either true or false
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setCleanupListsAndFilters(
        $mailingId,
        $cleanup
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        if ($cleanup === true) {
            $cleanup = 'true';
        } elseif ($cleanup === false) {
            $cleanup = 'false';
        }

        return $this->post(
            "mailings/$encodedMailingId/settings/post_sendout_cleanup",
            "<cleanup>$cleanup</cleanup>"
        );
    }

    /**
     * Retrieve the cleanup option for post sendout processing.
     * This flag defines if the used contact list and filter should be deleted after sendout.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with "true" or "false" available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function isCleanupListsAndFilters($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/settings/post_sendout_cleanup");
    }

    /**
     * Sets the dispatch logic for trigger mailings
     *
     * @param int    $mailingId the ID of the mailing
     * @param string $logic     the string representation of the logic (xml)
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setTriggerDispatchLogic(
        $mailingId,
        $logic
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->put(
            "mailings/$encodedMailingId/dispatching",
            $logic
        );
    }

    /**
     * Used for DOI Mailings
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setTriggerActive($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post("mailings/$encodedMailingId/dispatching/activate");
    }

    /**
     * Deletes an active trigger mailing.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteActiveTriggerMailing($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->delete("mailings/$encodedMailingId/dispatching");
    }

    /**
     * Deletes a mailing by ID.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteMailing($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->delete("mailings/$encodedMailingId");
    }

    /**
     * Updates the HTML content of the mailing referenced by the given ID.
     *
     * @param int    $mailingId       the ID of the mailing
     * @param string $html            the new HTML content of the mailing
     * @param bool   $doImageGrabbing specifies if image grabbing should be performed
     * @param bool   $doLinkTracking  specifies if link tracking should be performed
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setHTMLContent(
        $mailingId,
        $html,
        $doImageGrabbing = true,
        $doLinkTracking = false
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = [
            'doImageGrabbing' => $doImageGrabbing === true ? 'true' : 'false',
            'doLinkTracking'  => $doLinkTracking === true ? 'true' : 'false',
        ];

        return $this->post(
            "mailings/$encodedMailingId/contents/html",
            $html,
            $queryParameters,
            'text/html'
        );
    }

    /**
     * Updates the TEXT content of the mailing referenced by the given ID.
     *
     * @param int    $mailingId the ID of the mailing
     * @param string $text      the new TEXT content of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setTextContent(
        $mailingId,
        $text
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/contents/text",
            $text,
            [],
            'text/plain'
        );
    }

    /**
     * Fetches the HTML content of the mailing identified by the given ID.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the HTML content string of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getHTMLContent($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get(
            "mailings/$encodedMailingId/contents/html",
            null,
            'text/html'
        );
    }

    /**
     * Fetches the TEXT content of the mailing identified by the given ID.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the TEXT content string of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTextContent($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get(
            "mailings/$encodedMailingId/contents/text",
            null,
            'text/plain'
        );
    }


    /**
     * Adds a contactfilter to the contact filter restrictions of the mailing with the given ID.
     *
     * @param int $mailingId       the ID of the mailing
     * @param int $contactFilterId the ID of the contact filter
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function addContactFilterRestriction(
        $mailingId,
        $contactFilterId
    ) {
        $encodedMailingId       = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));
        $encodedContactFilterId = rawurlencode(mb_convert_encoding((string) $contactFilterId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/restrictions/$encodedContactFilterId",
            null
        );
    }

    /**
     * Removes a contactfilter from the contact filter restrictions of the mailing with the given ID.
     *
     * @param int $mailingId       the ID of the mailing
     * @param int $contactFilterId the ID of the contact filter
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteContactFilterRestriction(
        $mailingId,
        $contactFilterId
    ) {
        $encodedMailingId       = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));
        $encodedContactFilterId = rawurlencode(mb_convert_encoding((string) $contactFilterId, 'UTF-8'));

        return $this->delete(
            "mailings/$encodedMailingId/restrictions/$encodedContactFilterId",
            null
        );
    }

    /**
     * Retrieve the number of contact filter restrictions set for the mailing with the given ID.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getContactFilterRestrictionsCount($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get(
            "mailings/$encodedMailingId/restrictions/count",
            null
        );
    }


    /**
     * Updates the target group id of the mailing referenced by the given ID.
     *
     * @param int    $mailingId     the ID of the mailing
     * @param string $targetGroupId the ID of the target group to set
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setTargetGroupId(
        $mailingId,
        $targetGroupId
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/targetgroupid",
            "<targetgroupid>$targetGroupId</targetgroupid>"
        );
    }

    /**
     * Fetches the target group id of the mailing identified by the given ID.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the target group id of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTargetGroupId($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get(
            "mailings/$encodedMailingId/targetgroupid",
            null
        );
    }

    /**
     * Updates the sender email address of the mailing referenced by the given ID. <br />
     * Note: if not only the local part but also the domain is provided, make sure that is exists in Maileon.
     *
     * @param int    $mailingId the ID of the mailing
     * @param string $email     the ID of the target group to set
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setSender(
        $mailingId,
        $email
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/contents/sender",
            "<sender><![CDATA[$email]]></sender>"
        );
    }

    /**
     * Fetches the sender email address of the mailing identified by the given ID.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the sender email address of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getSender($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/contents/sender");
    }

    /**
     * Fetches the state of the mailing identified by the given ID.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the state of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getState($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/state");
    }

    /**
     * Updates the subject of the mailing referenced by the given ID.
     *
     * @param int    $mailingId the ID of the mailing
     * @param string $subject   the subject of the mailing to set
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setSubject(
        $mailingId,
        $subject
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/contents/subject",
            "<subject><![CDATA[$subject]]></subject>"
        );
    }

    /**
     * Fetches the subject of the mailing identified by the given ID.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the subject of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getSubject($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/contents/subject");
    }

    /**
     * Updates the preview text of the mailing referenced by the given ID.
     *
     * @param int    $mailingId   the ID of the mailing
     * @param string $previewText the preview text of the mailing to set, limit: 255 characters
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setPreviewText(
        $mailingId,
        $previewText
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/contents/previewtext",
            "<previewtext><![CDATA[$previewText]]></previewtext>"
        );
    }

    /**
     * Fetches the preview text of the mailing identified by the given ID.
     *
     * @param int $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the preview text of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getPreviewText($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/contents/previewtext");
    }

    /**
     * Sets the template for a mailing. Be careful, all HTML/text contents will be resettet.
     * For templates from the same account, relative paths can be used in the form
     * "my template" of with sub folders "someSubFolder/my template".
     * For shared templates, an absolute path is required. The easiest way to find the
     * correct path is to set the template
     * manually and use getTemplate() to retrieve the name.
     *
     * @param int    $mailingId the ID of the mailing
     * @param string $template  the template id of the mailing to set
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setTemplate(
        $mailingId,
        $template
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->put(
            "mailings/$encodedMailingId/template",
            "<templateId><![CDATA[$template]]></templateId>"
        );
    }

    /**
     * Returns the template of the mailing with the provided id.
     * For templates from the same account, relative paths will be returned
     * in the form "my template" of with sub folders "someSubFolder/my template".
     * For shared templates, an absolute path is returned.
     *
     * @param int $mailingId the ID of the mailing the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the the corresponding template id of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTemplate($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/template");
    }

    /**
     * Resets the HTML/text contents of the mailing to its template state.
     *
     * @param string $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function resetContentsToTemplate($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->put("mailings/$encodedMailingId/contents/reset");
    }

    /**
     * Updates the senderalias of the mailing referenced by the given ID. <br />
     *
     * @param string $mailingId   the ID of the mailing
     * @param string $senderalias the sender alias to set
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setSenderAlias(
        $mailingId,
        $senderalias
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/contents/senderalias",
            "<senderalias><![CDATA[$senderalias]]></senderalias>"
        );
    }

    /**
     * Fetches the sender alias of the mailing identified by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the sender alias of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getSenderAlias($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/contents/senderalias");
    }

    /**
     * Updates the recipientalias of the mailing referenced by the given ID. <br />
     *
     * @param string $mailingId      the ID of the mailing
     * @param string $recipientalias the recipient alias to set
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setRecipientAlias(
        $mailingId,
        $recipientalias
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/contents/recipientalias",
            "<recipientalias><![CDATA[$recipientalias]]></recipientalias>"
        );
    }

    /**
     * Fetches the reply-to address of the mailing identified by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the reply-to address of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getReplyToAddress($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/settings/replyto");
    }

    /**
     * Sets the reply-to address of the mailing identified by the given ID.
     *
     * @param string $mailingId   the ID of the mailing
     * @param bool   $auto        (default = true) If true, the Maileon autorecognition will be used and emails will be saved within
     *                            Maileon. If false, a custom email address can be passed which gets all mails forwarded.
     * @param string $customEmail (default = empty) If $auto is false, this email will be used for manual responses.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setReplyToAddress(
        $mailingId,
        $auto = true,
        $customEmail = null
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = [
            'auto'        => $auto === true ? 'true' : 'false',
            'customEmail' => $customEmail,
        ];

        return $this->post(
            "mailings/$encodedMailingId/settings/replyto",
            null,
            $queryParameters
        );
    }

    /**
     *
     * Method to retrieve mailings by scheduling time
     *
     * @param string $scheduleTime         This is a date and time string that defines the filter for a mailing. The mailings before and
     *                                     after that time can be queried, see beforeSchedulingTime. The format is the standard SQL date:
     *                                     yyyy-MM-dd HH:mm:ss
     * @param bool   $beforeSchedulingTime (default = true) If true, the mailings before the given time will be returned, if false, the
     *                                     mailings at or after the given time will be returned.
     * @param array  $fields
     * @param int    $page_index
     * @param int    $page_size
     * @param string $orderBy
     * @param string $order
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getMailingsBySchedulingTime(
        $scheduleTime,
        $beforeSchedulingTime = true,
        $fields = [],
        $page_index = 1,
        $page_size = 100,
        $orderBy = 'id',
        $order = 'DESC'
    ) {
        $queryParameters = [
            'page_index'           => $page_index,
            'page_size'            => $page_size,
            'scheduleTime'         => urlencode($scheduleTime),
            'beforeSchedulingTime' => $beforeSchedulingTime === true ? 'true' : 'false',
            'orderBy'              => $orderBy,
            'order'                => $order,
        ];

        $queryParameters = $this->appendArrayFields($queryParameters, 'fields', $fields);

        return $this->get(
            'mailings/filter/scheduletime',
            $queryParameters
        );
    }


    /**
     *
     * Method to retrieve mailings by keywords
     *
     * @param string[] $keywords This is the list of keywords to filter for
     * @param string   $keywordsOp
     * @param array    $fields
     * @param int      $page_index
     * @param int      $page_size
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getMailingsByKeywords(
        $keywords,
        $keywordsOp = 'and',
        $fields = [],
        $page_index = 1,
        $page_size = 100
    ) {
        $queryParameters = [
            'page_index' => $page_index,
            'page_size'  => $page_size,
            'order'      => 'DESC',
        ];

        $queryParameters = $this->appendArrayFields($queryParameters, 'keywords', $keywords);
        $queryParameters = $this->appendArrayFields($queryParameters, 'keywordsOp', $keywordsOp);
        $queryParameters = $this->appendArrayFields($queryParameters, 'fields', $fields);

        return $this->get(
            'mailings/filter/keywords',
            $queryParameters
        );
    }

    /**
     *
     * Method to retrieve mailings by types
     *
     * Types can be selected from 'doi','trigger', 'trigger_template' or 'regular'
     *
     * @param string[] $types This is the list of types to filter for
     * @param array    $fields
     * @param int      $page_index
     * @param int      $page_size
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     * @see MailingFields
     */
    public function getMailingsByTypes(
        $types,
        $fields = [],
        $page_index = 1,
        $page_size = 100
    ) {
        $queryParameters = [
            'page_index' => $page_index,
            'page_size'  => $page_size,
            'order'      => 'DESC',
        ];

        $queryParameters = $this->appendArrayFields($queryParameters, 'types', $types);
        $queryParameters = $this->appendArrayFields($queryParameters, 'fields', $fields);

        return $this->get(
            'mailings/filter/types',
            $queryParameters
        );
    }

    /**
     * Method to retrieve mailings by states
     *
     * @param array $states     States can be selected from 'draft', 'failed', 'queued', 'checks', 'blacklist', 'preparing', 'sending',
     *                          'canceled', 'paused', 'done', 'archiving', 'archived', 'released', and 'scheduled' (=waiting to be sent)
     * @param array $fields
     * @param int   $page_index (default = 1) The index of the result page. The index must be greater or equal to 1.
     * @param int   $page_size
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     * @see MailingFields
     */
    public function getMailingsByStates(
        $states,
        $fields = [],
        $page_index = 1,
        $page_size = 100
    ) {
        $queryParameters = [
            'page_index' => $page_index,
            'page_size'  => $page_size,
            'order'      => 'DESC',
        ];

        $queryParameters = $this->appendArrayFields($queryParameters, 'states', $states);
        $queryParameters = $this->appendArrayFields($queryParameters, 'fields', $fields);

        return $this->get(
            'mailings/filter/states',
            $queryParameters
        );
    }

    /**
     * Schedules the mailing to be instantly sent
     *
     * @param int $mailingId The ID of the mailing to send now
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function sendMailingNow($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post("mailings/$encodedMailingId/sendnow");
    }

    /**
     * Schedules the mailing for a given time. If <code>dispatchOption</code> is set, the enhanced scheduling options are enabled.
     *
     * @param int     $mailingId                 The ID of the mailing to schedule
     * @param string  $date                      The SQL conform date of the schedule day in the format YYYY-MM-DD
     * @param string  $hours                     The schedule hour in the format of HH, 24 hours format
     * @param string  $minutes                   The schedule minutes in the format MM
     * @param string  $dispatchOption            The time distribution strategy to choose from {'hour', 'weekdayhour', 'uniform'}.
     * @param         $dispatchEndInHours        Number of hours beginning from the dispatch start util which the dispatch distribution over
     *                                           the time has to be finished. Used in case of 'hour' dispatch option and 'uniform' option.
     *                                           Allowed values for the 'uniform' distribution are in [2..96], whereas for 'hour' strategy
     *                                           they are ranging from [2..24].
     * @param         $dispatchEndInDays         Number of days beginning from the dispatch start util which the dispatch distribution over
     *                                           the time has to be finished. Used only with dispatch option 'weekdayhour' and its
     *                                           acceptable range is [1..7].
     * @param         $dispatchEndExactDatetime  The exact end date util which the dispatch time distribution has to be finished. It is used
     *                                           when none of the arguments above <code>dispatchEndInHours</code>,
     *                                           <code>dispatchEndInDays</code> aren't set i.e. equals 0. Note that one of
     *                                           <code>dispatchEndInHours</code>, <code>dispatchEndInDays</code>,
     *                                           <code>dispatchEndExactDatetime</code> argument should be used in the request according to
     *                                           the selected dispatch option. Format: yyyy-MM-dd HH:mm
     * @param boolean $clicksAsResponseReference The parameter determines the inclusion/exclusion of clicks as a response criteria when
     *                                           selecting {'hour', 'weekdayhour'} options.
     * @param int     $dispatchWavesGroup        The number determines how many consecutive sending waves will be grouped when using
     *                                           {'hour', 'weekdayhour'} distribution. Supported values are {1, 2, 3 (default)}.
     * @param string  $dispatchUniformInterval   The argument controls the interval {'hour', '30m', '20m', '15m', '10m'} for the 'uniform'
     *                                           strategy indicating the frequency of mailing distribution over time. It should equal null
     *                                           for {'hour', 'weekdayhour'} dispatch options.
     * @param string  $allowedHours              The value represents the allowed hours. Comma separated values for the allowed hours and
     *                                           can be combined with a range of hours. The required format looks like 0,3,5,17-21 as an
     *                                           example. The acceptable values range is 0..23. Note that if this argument is not provided,
     *                                           all 24H of the day will be considered as acceptable dispatch hours.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setMailingSchedule(
        $mailingId,
        $date,
        $hours,
        $minutes,
        $dispatchOption = null,
        $dispatchEndInHours = null,
        $dispatchEndInDays = null,
        $dispatchEndExactDatetime = null,
        $clicksAsResponseReference = null,
        $dispatchWavesGroup = null,
        $dispatchUniformInterval = null,
        $allowedHours = null
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = [
            'date'    => $date,
            'hours'   => $hours,
            'minutes' => $minutes,
        ];

        if (! empty($dispatchOption)) {
            $queryParameters ['dispatchOption'] = urlencode($dispatchOption);
        }

        if (! empty($dispatchEndInHours)) {
            $queryParameters ['dispatchEndInHours'] = urlencode($dispatchEndInHours);
        }

        if (! empty($dispatchEndInDays)) {
            $queryParameters ['dispatchEndInDays'] = urlencode($dispatchEndInDays);
        }

        if (! empty($dispatchEndExactDatetime)) {
            $queryParameters ['dispatchEndExactDatetime'] = urlencode($dispatchEndExactDatetime);
        }

        if (! empty($clicksAsResponseReference)) {
            $queryParameters ['clicksAsResponseReference'] = ((bool) $clicksAsResponseReference === true) ? 'true' : 'false';
        }

        if (! empty($dispatchWavesGroup)) {
            $queryParameters ['dispatchWavesGroup'] = urlencode($dispatchWavesGroup);
        }

        if (! empty($dispatchUniformInterval)) {
            $queryParameters ['dispatchUniformInterval'] = urlencode($dispatchUniformInterval);
        }

        if (! empty($allowedHours)) {
            $queryParameters ['allowedHours'] = urlencode($allowedHours);
        }

        return $this->put(
            "mailings/$encodedMailingId/schedule",
            '',
            $queryParameters
        );
    }

    /**
     * Delete the schedule for the given mailing
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteMailingSchedule($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->delete("mailings/$encodedMailingId/schedule");
    }

    /**
     * Get the schedule for the given mailing
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getMailingSchedule($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/schedule");
    }

    /**
     * Update the schedule for the given mailing. If <code>dispatchOption</code> is set, the enhanced scheduling options are enabled.
     *
     * @param int     $mailingId
     * @param date    $date                      The SQL conform date of the schedule day in the format YYYY-MM-DD
     * @param hours   $hours                     The schedule hour in the format of HH, 24 hours format
     * @param minute  $minutes                   The schedule minutes in the format MM
     * @param string  $dispatchOption            The time distribution strategy to choose from {'hour', 'weekdayhour', 'uniform'}.
     * @param         $dispatchEndInHours        Number of hours beginning from the dispatch start util which the dispatch distribution over
     *                                           the time has to be finished. Used in case of 'hour' dispatch option and 'uniform' option.
     *                                           Allowed values for the 'uniform' distribution are in [2..96], whereas for 'hour' strategy
     *                                           they are ranging from [2..24].
     * @param         $dispatchEndInDays         Number of days beginning from the dispatch start util which the dispatch distribution over
     *                                           the time has to be finished. Used only with dispatch option 'weekdayhour' and its
     *                                           acceptable range is [1..7].
     * @param         $dispatchEndExactDatetime  The exact end date util which the dispatch time distribution has to be finished. It is used
     *                                           when none of the arguments above <code>dispatchEndInHours</code>,
     *                                           <code>dispatchEndInDays</code> aren't set i.e. equals 0. Note that one of
     *                                           <code>dispatchEndInHours</code>, <code>dispatchEndInDays</code>,
     *                                           <code>dispatchEndExactDatetime</code> argument should be used in the request according to
     *                                           the selected dispatch option. Format: yyyy-MM-dd HH:mm
     * @param boolean $clicksAsResponseReference The parameter determines the inclusion/exclusion of clicks as a response criteria when
     *                                           selecting {'hour', 'weekdayhour'} options.
     * @param int     $dispatchWavesGroup        The number determines how many consecutive sending waves will be grouped when using
     *                                           {'hour', 'weekdayhour'} distribution. Supported values are {1, 2, 3 (default)}.
     * @param string  $dispatchUniformInterval   The argument controls the interval {'hour', '30m', '20m', '15m', '10m'} for the 'uniform'
     *                                           strategy indicating the frequency of mailing distribution over time. It should equal null
     *                                           for {'hour', 'weekdayhour'} dispatch options.
     * @param string  $allowedHours              The value represents the allowed hours. Comma separated values for the allowed hours and
     *                                           can be combined with a range of hours. The required format looks like 0,3,5,17-21 as an
     *                                           example. The acceptable values range is 0..23. Note that if this argument is not provided,
     *                                           all 24H of the day will be considered as acceptable dispatch hours.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function updateMailingSchedule(
        $mailingId,
        $date,
        $hours,
        $minutes,
        $dispatchOption = null,
        $dispatchEndInHours = null,
        $dispatchEndInDays = null,
        $dispatchEndExactDatetime = null,
        $clicksAsResponseReference = null,
        $dispatchWavesGroup = null,
        $dispatchUniformInterval = null,
        $allowedHours = null
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = [
            'date'    => $date,
            'hours'   => $hours,
            'minutes' => $minutes,
        ];

        if (! empty($dispatchOption)) {
            $queryParameters ['dispatchOption'] = urlencode($dispatchOption);
        }

        if (! empty($dispatchEndInHours)) {
            $queryParameters ['dispatchEndInHours'] = urlencode($dispatchEndInHours);
        }

        if (! empty($dispatchEndInDays)) {
            $queryParameters ['dispatchEndInDays'] = urlencode($dispatchEndInDays);
        }

        if (! empty($dispatchEndExactDatetime)) {
            $queryParameters ['dispatchEndExactDatetime'] = urlencode($dispatchEndExactDatetime);
        }

        if (! empty($clicksAsResponseReference)) {
            $queryParameters ['clicksAsResponseReference'] = ((bool) $clicksAsResponseReference === true) ? 'true' : 'false';
        }

        if (! empty($dispatchWavesGroup)) {
            $queryParameters ['dispatchWavesGroup'] = urlencode($dispatchWavesGroup);
        }

        if (! empty($dispatchUniformInterval)) {
            $queryParameters ['dispatchUniformInterval'] = urlencode($dispatchUniformInterval);
        }

        if (! empty($allowedHours)) {
            $queryParameters ['allowedHours'] = urlencode($allowedHours);
        }

        return $this->post(
            "mailings/$encodedMailingId/schedule",
            '',
            $queryParameters
        );
    }


    /**
     * Fetches the DOI mailing key of the mailing identified by the given ID.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, with the target group id of the mailing available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getDoiMailingKey($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get(
            "mailings/$encodedMailingId/settings/doi_key",
            null,
            'text/html'
        );
    }

    /**
     * Sets the key of the DOI mailing identified by the given ID.
     *
     * @param int    $mailingId The ID of the mailing
     * @param string $doiKey    The new DOI key.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setDoiMailingKey(
        $mailingId,
        $doiKey
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/settings/doi_key",
            "<doi_key>$doiKey</doi_key>"
        );
    }

    /**
     * Deactivates a trigger mailing by ID.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deactivateTriggerMailing($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->delete("mailings/$encodedMailingId/dispatching");
    }


    /**
     * Get the dispatch data for a trigger mailing by mailing ID.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTriggerDispatchLogic($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/dispatching");
    }


    /**
     * Get the schedule for regular mailings by mailing ID.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getSchedule($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/schedule");
    }

    /**
     * Get the archive url for the mailing ID.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getArchiveUrl($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/archiveurl");
    }

    /**
     * Get the report url for the mailing ID.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getReportUrl($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/reporturl");
    }

    /**
     * Updates the name of the mailing referenced by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     * @param string $name      the name of the mailing to set
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setName(
        $mailingId,
        $name
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/name",
            "<name><![CDATA[$name]]></name>"
        );
    }

    /**
     * Get the name for the mailing by mailing ID.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getName($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/name");
    }

    /**
     * Updates the tags of the mailing referenced by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     * @param array  $tags      the tags
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setTags(
        $mailingId,
        $tags
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/settings/tags",
            '<tags><![CDATA[' . implode('#', $tags) . ']]></tags>'
        );
    }

    /**
     * Get the tags for the mailing identified by mailing ID.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTags($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/settings/tags");
    }

    /**
     * Updates the locale of the mailing referenced by the given ID.
     *
     * @param string $mailingId the ID of the mailing
     * @param string $locale    the locale in the form xx: e.g. de, en, fr, �
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setLocale(
        $mailingId,
        $locale
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post(
            "mailings/$encodedMailingId/settings/locale",
            "<locale>$locale</locale>"
        );
    }

    /**
     * Get the locale for the mailing identified by mailing ID in the form xx: e.g. de, en, fr
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getLocale($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/settings/locale");
    }

    /**
     * Execute the RSS SmartMailing functionality for mailings, i.e. fill all SmartMailing
     * Tags from the described RSS-Feeds.
     * For more information about RSS-SmartMailing, please check our customer support at service@xqueue.com
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function fillRssSmartContentTags($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post("mailings/$encodedMailingId/contents/smartmailing/rss");
    }

    /**
     * Copy the mailing with the given mailing ID.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function copyMailing($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->post("mailings/$encodedMailingId/copy");
    }

    /**
     * Read a binary file from the file system and adds it as an attachment to this transaction.
     *
     * @param int    $mailingId          The ID of the mailing
     * @param string $filename
     * @param string $contentType
     * @param string $attachmentFileName Name of the file in the attachments
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function addAttachmentFromFile(
        $mailingId,
        $filename,
        $contentType,
        $attachmentFileName = null
    ) {
        $handle = fopen($filename, "rb");

        if (false === $handle) {
            throw new MaileonAPIException("Cannot read file $filename.");
        }

        $contents = '';

        while (! feof($handle)) {
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
     * @param int    $mailingId   The mailing id
     * @param string $filename    Filename of the attachment to be displayed in sent emails. It is recommended to keep the filename short
     *                            and to use an extension corresponding to the mime type of the attachment.
     * @param string $contentType The mime type of the attachment
     * @param string $contents    The file content
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function addAttachment(
        $mailingId,
        $filename,
        $contentType,
        $contents
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = ['filename' => urlencode($filename)];

        return $this->post(
            "mailings/$encodedMailingId/attachments",
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
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getAttachments($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/attachments");
    }

    /**
     * Returns the attachment with the provided id as a file.
     *
     * @param int $mailingId The ID of the mailing
     * @param int $attachmentId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getAttachment(
        $mailingId,
        $attachmentId
    ) {
        $encodedMailingId    = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));
        $encodedAttachmentId = rawurlencode(mb_convert_encoding((string) $attachmentId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/attachments/$encodedAttachmentId");
    }

    /**
     * Returns the count of available attachments in the mailing with the provided id.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getAttachmentsCount($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/attachments/count");
    }

    /**
     * Deletes all the attachments that belong to the mailing with the provided id. The mailing should not be sealed.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteAttachments($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->delete("mailings/$encodedMailingId/attachments/");
    }

    /**
     * Deletes the attachment with the provided id from the mailing. The mailing should not be sealed.
     *
     * @param int $mailingId The ID of the mailing
     * @param int $attachmentId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteAttachment(
        $mailingId,
        $attachmentId
    ) {
        if (empty($attachmentId)) {
            throw new MaileonAPIException('no attachment id specified');
        }

        $encodedMailingId    = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));
        $encodedAttachmentId = rawurlencode(mb_convert_encoding((string) $attachmentId, 'UTF-8'));

        return $this->delete("mailings/$encodedMailingId/attachments/$encodedAttachmentId");
    }

    /**
     * Copies the attachments of a source mailing into a target mailing. Note that the target
     * mailing should not be sealed and that the resulting total count of attachments in the
     * target mailing should not exceed 10.
     *
     * @param int $mailingId The ID of the mailing
     * @param int $srcMailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function copyAttachments(
        $mailingId,
        $srcMailingId
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = ['src_mailing_id' => $srcMailingId];

        return $this->put(
            "mailings/$encodedMailingId/attachments",
            '',
            $queryParameters
        );
    }

    /**
     * Returns a list of custom properties for the mailing with the provided id.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getCustomProperties($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/settings/properties");
    }


    /**
     * Adds a list of custom properties to the mailing with the provided id.
     *
     * @param int   $mailingId  The ID of the mailing
     * @param array $properties Array of CustomProperty or single property
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function addCustomProperties(
        $mailingId,
        $properties
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $xml = new SimpleXMLElement('<?xml version="1.0"?><properties></properties>');

        if (is_array($properties)) {
            foreach ($properties as $property) {
                $this->sxmlAppend($xml, $property->toXML());
            }
        } else {
            $this->sxmlAppend($xml, $properties->toXML());
        }

        return $this->post(
            "mailings/$encodedMailingId/settings/properties",
            $xml->asXML()
        );
    }


    /**
     * Updates a custom property of the mailing with the provided id.
     *
     * @param int            $mailingId The ID of the mailing
     * @param CustomProperty $property
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function updateCustomProperty(
        $mailingId,
        $property
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = [
            'name'  => $property->key,
            'value' => $property->value,
        ];

        return $this->put(
            "mailings/$encodedMailingId/settings/properties",
            '',
            $queryParameters
        );
    }


    /**
     * Deletes a custom property of the mailing with the provided id.
     *
     * @param int    $mailingId    The ID of the mailing
     * @param string $propertyName The name of the property to delete
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteCustomProperty(
        $mailingId,
        $propertyName
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = ['name' => $propertyName];

        return $this->delete(
            "mailings/$encodedMailingId/settings/properties",
            $queryParameters
        );
    }


    /**
     * Sends a testmail for the mailing with the provided id to a given email address.
     * If the email address does not exist within your contacts,
     * the personalization is done according to your default personalization user configured in Maileon.
     * <p><a href="https://support.maileon.com/support/mailing-send-testmail-to-single-emailaddress/">Documentation website</a></p>
     *
     * @param int $mailingId id of existing mailing
     * @param     $email     email address
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function sendTestMail(
        $mailingId,
        $email
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = ['email' => $email];

        return $this->post(
            "mailings/$encodedMailingId/sendtestemail",
            '',
            $queryParameters
        );
    }

    /**
     * Sends a testmail for the mailing with the provided id to a test-targetgroup given by its ID.
     *
     * @param int $mailingId
     * @param int $testTargetGroupId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function sendTestMailToTestTargetGroup(
        $mailingId,
        $testTargetGroupId
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = ['test_targetgroup_id' => $testTargetGroupId];

        return $this->post(
            "mailings/$encodedMailingId/checks/testsendout",
            '',
            $queryParameters
        );
    }

    /**
     * Assigns a mailing blacklist to a mailing.
     *
     * @param int $mailingId          id of the existing mailing
     * @param int $mailingBlacklistId id of the mailing blacklist to be assigned to the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function addMailingBlacklist(
        $mailingId,
        $mailingBlacklistId
    ) {
        $encodedMailingId          = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));
        $encodedMailingBlacklistId = rawurlencode(mb_convert_encoding((string) $mailingBlacklistId, 'UTF-8'));

        return $this->post("mailings/$encodedMailingId/mailingblacklists/$encodedMailingBlacklistId");
    }

    /**
     * Deletes a mailing blacklist from a mailing.
     *
     * @param int $mailingId
     * @param int $mailingBlacklistId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteMailingBlacklist(
        $mailingId,
        $mailingBlacklistId
    ) {
        $encodedMailingId          = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));
        $encodedMailingBlacklistId = rawurlencode(mb_convert_encoding((string) $mailingBlacklistId, 'UTF-8'));

        return $this->delete("mailings/$encodedMailingId/mailingblacklists/$encodedMailingBlacklistId");
    }


    /**
     * Retrieve the domain of this mailing
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getMailingDomain($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/domain/");
    }

    /**
     * Retrieve all blacklists assigned to this mailing
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getMailingBlacklists($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/mailingblacklists/");
    }

    public function sxmlAppend(
        SimpleXMLElement $to,
        SimpleXMLElement $from
    ) {
        $toDom   = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }

    /**
     * Get the configured recipient alias for the given mailing
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getRecipientAlias($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/contents/recipientalias");
    }

    /**
     * Get the tracking strategy for the given mailing
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTrackingStrategy($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/settings/trackingstrategy");
    }

    /**
     * Get the configured speed level for the given mailing
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getSpeedLevel($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/settings/speedlevel");
    }

    /**
     * Get the configured post sendout cleanup state for the given mailing
     *
     * @param int $mailingId
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getPostSendoutCleanupState($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get("mailings/$encodedMailingId/settings/post_sendout_cleanup");
    }

    /**
     * Grabs images for the CMS2 media library from the specified HTML content for the mailing referenced by the given ID.
     * Returns encountered errors and the transformed HTML.
     *
     * @param int    $mailingId         The ID of the mailing
     * @param string $html              The HTML content to grab images from
     * @param bool   $destinationFolder specifies the media library path
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function cms2GrabImages(
        $mailingId,
        $html,
        $destinationFolder = ''
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = ['destinationFolder' => urlencode($destinationFolder)];

        return $this->post(
            "mailings/$encodedMailingId/cms2/contents/grab_images",
            $html,
            $queryParameters,
            'application/json',
            ImageGrabbingResult::class,
            'text/html'
        );
    }

    /**
     * Returns the mailing as a byte array as possible in the UI. The archive contains the HTML code as well as the linked images.
     *
     * @param int     $mailingId                    The ID of the mailing
     * @param boolean $removeTemplateLanguageMarkup defines if the  Maileon Markup Language should be removed from the HTML or not
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function cms2GetMailingAsZip(
        $mailingId,
        $removeTemplateLanguageMarkup = true
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = ['removeTemplateLanguageMarkup' => $removeTemplateLanguageMarkup ? 'true' : 'false'];

        return $this->get(
            "mailings/$encodedMailingId/cms2/contents",
            $queryParameters,
            'application/json'
        );
    }

    /**
     * Upload the mailing content from a Maileon Zip file as possible in the UI.
     * The archive contains the HTML code as well as the linked images.
     *
     * @param int    $mailingId The ID of the mailing
     * @param string $filename
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function cms2SetMailingFromZipFromFile(
        $mailingId,
        $filename
    ) {
        // Read the file
        $handle = fopen($filename, "rb");

        if (false === $handle) {
            throw new MaileonAPIException("Cannot read file $filename.");
        }

        $fileContent = '';

        while (! feof($handle)) {
            $fileContent .= fread($handle, 8192);
        }

        fclose($handle);

        return $this->cms2SetMailingFromZipFromBase64($mailingId, base64_encode($fileContent));
    }

    /**
     * @param int $mailingId
     * @param     $base64Content
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function cms2SetMailingFromZipFromBase64(
        $mailingId,
        $base64Content
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $data = [
            'content' => $base64Content,
        ];

        $data = json_encode($data);

        return $this->put(
            "mailings/$encodedMailingId/cms2/contents",
            $data,
            [],
            'application/json'
        );
    }

    /**
     * Saves a CMS2 mailing as a template in the media library for later access.
     *
     * @param int     $mailingId                    the ID of the mailing
     * @param string  $templatePath                 the path including the filename where the template will be placed inside the media
     *                                              library
     * @param boolean $removeTemplateLanguageMarkup defines if the  Maileon Markup Language should be removed from the HTML or not
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function cms2SaveMailingToFolder(
        $mailingId,
        $templatePath,
        $removeTemplateLanguageMarkup = false
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = [
            'templatePath'                 => urlencode($templatePath),
            'removeTemplateLanguageMarkup' => $removeTemplateLanguageMarkup ? 'true' : 'false',
        ];

        return $this->post(
            "mailings/$encodedMailingId/cms2/contents", '',
            $queryParameters
        );
    }

    /**
     * Sets a template from the media library to a CMS2 mailing.
     *
     * @param int    $mailingId    the ID of the mailing
     * @param string $templatePath the path including the filename where the template is in the media library
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function cms2SetTemplate(
        $mailingId,
        $templatePath
    ) {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        $queryParameters = ['templatePath' => urlencode($templatePath)];

        return $this->put(
            "mailings/$encodedMailingId/cms2/contents", '',
            $queryParameters
        );
    }

    /**
     * Returns the mailing thumbnail as a byte array.
     *
     * @param int $mailingId The ID of the mailing
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function cms2GetThumbnail($mailingId)
    {
        $encodedMailingId = rawurlencode(mb_convert_encoding((string) $mailingId, 'UTF-8'));

        return $this->get(
            "mailings/$encodedMailingId/cms2/contents/thumbnail",
            [],
            'application/json'
        );
    }
}
