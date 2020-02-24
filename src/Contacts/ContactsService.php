<?php

namespace Maileon\Contacts;

use Maileon\Contacts\Contact;
use Maileon\AbstractMaileonService;

/**
 * This service wrapps the REST API calls for the contact features.
 *
 * @author Felix Heinrichs | Trusted Mails GmbH |
 * <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class ContactsService extends AbstractMaileonService
{

    /**
     * Creates or updates a contact and optionally triggers a double opt-in (doi) process.
     * Note that none of the attributes is required.
     *
     * @param Contact $contact
     *  the contact to create or update; if no permission is set, the Maileon default permission "NONE" will be used
     * @param SynchronizationMode $syncMode
     *  the synchronization mode to employ
     * @param string $src
     *  A string intended to describe the source of the contact.
     *  If provided, the string will be stored with the doi process.
     * @param string $subscriptionPage
     *  In case where this method was called by a subscription page,
     *  this string offers the possibility to keep track of it for use in reports.
     * @param bool $doi
     *  Tells whether a double opt-in process should be started for the created contact.
     *  Note that the status code returned for this request does not mean that the doi
     *  process succeeded.
     * @param bool $doiPlus
     *  This parameter is ignored if doi is not provided or false. In case the doi
     *  process succeeds, Maileon will be allowed to track opens and clicks of the contact.
     * @param string $doiMailingKey
     *  This parameter is ignored if doi is not provided or false. References the
     *  doi mailing to be used. If not provided, the default doi mailing will be used.
     * @return \em MaileonAPIResult
     *  the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function createContact(
        $contact,
        $syncMode,
        $src = "",
        $subscriptionPage = "",
        $doi = false,
        $doiPlus = false,
        $doiMailingKey = ""
    ) {
        $queryParameters = array(
            'sync_mode' => $syncMode->getCode(),
            'src' => urlencode($src),
            'subscription_page' => urlencode($subscriptionPage),
            'doi' => ($doi == true) ? "true" : "false",
            'doiplus' => ($doiPlus == true) ? "true" : "false"
        );


        // As empty does not work with return values (sometimes?), first trim the variable, then check it
        $doiMailingKey = trim($doiMailingKey);
        if (!empty($doiMailingKey)) {
            $queryParameters['doimailing'] = $doiMailingKey;
        }

        if (isset($contact->permission)) {
            $queryParameters['permission'] = $contact->permission->getCode();
        }

        // The API allows only some of the fields to be submitted
        $contactToSend = new Contact(
            null,
            $contact->email,
            null,
            $contact->external_id,
            null,
            $contact->standard_fields,
            $contact->custom_fields
        );

        return $this->post("contacts/email/" . $contactToSend->email, $contactToSend->toXMLString(), $queryParameters);
    }

    /**
     * Creates or updates a contact based on the external ID and optionally triggers a double opt-in (doi) process.
     * Note that none of the attributes is required.
     * Also note: this call returns 409 Conflict if more then one contact with the given external ID
     * exists as it is impossible to determine the correct contact to update.
     *
     * @param Contact $contact
     *  the contact to create or update
     * @param SynchronizationMode $syncMode
     *  the synchronization mode to employ
     * @param string $src
     *  A string intended to describe the source of the contact.
     *  If provided, the string will be stored with the doi process.
     * @param string $subscriptionPage
     *  In case where this method was called by a subscription page,
     *  this string offers the possibility to keep track of it for use in reports.
     * @param bool $doi
     *  Tells whether a double opt-in process should be started for the created contact.
     *  Note that the status code returned for this request does not mean that the doi
     *  process succeeded.
     * @param bool $doiPlus
     *  This parameter is ignored if doi is not provided or false. In case the doi
     *  process succeeds, Maileon will be allowed to track opens and clicks of the contact.
     * @param string $doiMailingKey
     *  This parameter is ignored if doi is not provided or false. References the
     *  doi mailing to be used. If not provided, the default doi mailing will be used.
     * @return \em MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function createContactByExternalId(
        $contact,
        $syncMode,
        $src = "",
        $subscriptionPage = "",
        $doi = false,
        $doiPlus = false,
        $doiMailingKey = ""
    ) {
        $queryParameters = array(
            'permission' => $contact->permission->getCode(),
            'sync_mode' => $syncMode->getCode(),
            'src' => urlencode($src),
            'subscription_page' => urlencode($subscriptionPage),
            'doi' => ($doi == true) ? "true" : "false",
            'doiplus' => ($doiPlus == true) ? "true" : "false",
            'doimailing' => trim($doiMailingKey)
        );

        // The API allows only some of the fields to be submitted
        $contactToSend = new Contact(
            null,
            $contact->email,
            null,
            $contact->external_id,
            null,
            $contact->standard_fields,
            $contact->custom_fields
        );

        return $this->post(
            "contacts/externalid/" . $contactToSend->external_id,
            $contactToSend->toXMLString(),
            $queryParameters
        );
    }

    /**
     * Return a contact using the maileon contact id. This resource is intended to be used
     * in profile update pages to prefill profile update forms. In order to prevent form
     * fields manipulation, a checksum of the maileon contact id is required as parameter.
     * Please refer to the documentation of the profile update pages for more details
     * about how to get the maileon contact id and the corresponding checksum.
     *
     * @param string $contactId
     *  the maileon contact id
     * @param string $checksum
     *  the checksum of the maileon contact id
     * @param string[] $standard_fields
     *  the standard fields to retrieve with the contact
     * @param string[] $custom_fields
     *  the custom fields to retrieve with the contact
     * @param bool $ignoreChecksum
     *  if set to true, no checksum is required
     * @return \em MaileonAPIResult
     *    the result object of the API call, with a Contact
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getContact(
        $contactId,
        $checksum,
        $standard_fields = array(),
        $custom_fields = array(),
        $ignoreChecksum = false
    ) {
        $queryParameters = array(
            'id' => $contactId,
            'checksum' => $checksum,
            'standard_field' => $standard_fields,
            'ignore_checksum' => $ignoreChecksum ? "true" : "false"
        );

        $queryParameters = $this->appendArrayFields($queryParameters, 'custom_field', $custom_fields);

        return $this->get('contacts/contact', $queryParameters);
    }

    /**
     * This method returns the number of contacts in the maileon newsletter account.
     *
     * @return \em MaileonAPIResult
     *    the result object of the API call, with the count of contacts
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getContactsCount()
    {
        return $this->get('contacts/count');
    }

    /**
     * Returns a page of contacts in the account.
     *
     * @param number $page_index
     *  the index of the result page to fetch
     * @param number $page_size
     *  the number of results to fetch per page
     * @param string[] $standard_fields
     *  the standard fields to retrieve for the contacts
     * @param string[] $custom_fields
     *  the custom fields to retrieve for the contacts
     * @return \em MaileonAPIResult
     *    the result object of the API call, with a Contacts
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getContacts($page_index = 1, $page_size = 100, $standard_fields = array(), $custom_fields = array())
    {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size,
            'standard_field' => $standard_fields
        );

        $queryParameters = $this->appendArrayFields($queryParameters, 'custom_field', $custom_fields);

        return $this->get('contacts', $queryParameters);
    }

    /**
     * Returns a contact with the provided email address.
     *
     * @param string $email
     *  the email address to retrieve a contact for
     * @param string[] $standard_fields
     *  the standard fields to return with the contact
     * @param string[] $custom_fields
     *  the custom fields to return with the contact
     * @return \em MaileonAPIResult
     *    the result object of the API call, with a Contact
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getContactByEmail($email, $standard_fields = array(), $custom_fields = array())
    {
        $queryParameters = array(
            'standard_field' => $standard_fields
        );

        $queryParameters = $this->appendArrayFields($queryParameters, 'custom_field', $custom_fields);

        return $this->get('contacts/email/' . utf8_encode($email), $queryParameters);
    }

    /**
     * Returns a list of contacts with the provided email address.
     *
     * @param string $email
     *  the email address to retrieve a contact for
     * @param string[] $standard_fields
     *  the standard fields to return with the contact
     * @param string[] $custom_fields
     *  the custom fields to return with the contact
     * @return \em MaileonAPIResult
     *  the result object of the API call, with an array of Contact
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getContactsByEmail($email, $standard_fields = array(), $custom_fields = array())
    {
        $queryParameters = array(
            'standard_field' => $standard_fields
        );

        $queryParameters = $this->appendArrayFields($queryParameters, 'custom_field', $custom_fields);

        return $this->get('contacts/emails/' . utf8_encode($email), $queryParameters);
    }

    /**
     * Retrieves all contacts with a given external ID.
     *
     * @param string $externalId the external ID to search for
     * @param string[] $standard_fields
     * @param string[] $custom_fields
     * @return \em MaileonAPIResult
     *  the result object of the API call, with a Contacts
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getContactsByExternalId($externalId, $standard_fields = array(), $custom_fields = array())
    {
        $queryParameters = array(
            'standard_field' => $standard_fields
        );

        $queryParameters = $this->appendArrayFields($queryParameters, 'custom_field', $custom_fields);

        return $this->get('contacts/externalid/' . utf8_encode($externalId), $queryParameters);
    }

    /**
     * Retrieves all contacts with a given contact filter ID.
     *
     * @param string $filterId the filter ID to use to select contacts
     * @param string[] $standard_fields
     * @param string[] $custom_fields
     * @return \em MaileonAPIResult
     *  the result object of the API call, with a Contacts
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getContactsByFilterId(
        $filterId,
        $page_index = 1,
        $page_size = 100,
        $standard_fields = array(),
        $custom_fields = array()
    ) {
        $queryParameters = array(
            'page_index' => $page_index,
            'page_size' => $page_size,
            'standard_field' => $standard_fields
        );
        $queryParameters = $this->appendArrayFields($queryParameters, 'custom_field', $custom_fields);
        return $this->get('contacts/filter/' . utf8_encode($filterId), $queryParameters);
    }

    /**
     * Retrieves the number of contacts matching a given contact filter ID.
     *
     * @param string $filterId the filter ID to use to select contacts
     * @return \em MaileonAPIResult
     *  the result object of the API call, with the number
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getCountContactsByFilterId($filterId)
    {
        return $this->get('contacts/filter/' . utf8_encode($filterId) . '/count');
    }

    /**
     * Retrieves the number of active contacts matching a given contact filter ID.
     *
     * @param string $filterId the filter ID to use to select contacts
     * @return \em MaileonAPIResult
     *    the result object of the API call, with the number
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getCountActiveContactsByFilterId($filterId)
    {
        return $this->get('contacts/filter/' . utf8_encode($filterId) . '/count/active');
    }

    /**
     * This methods updates the data of a Maileon contact identifying a contact by its internal Maileon ID
     *
     * @param Contact $contact
     *  The contact object to send to Maileon.
     * @param string $checksum
     *  This is the checksum that must be used when the request comes from a user,
     *  see documentation under http://dev.maileon.com for details.
     * @param string $src
     *  The source that shall be passed to the API.
     * @param string $subscriptionPage
     *  The subscription page the request comes from.
     * @param boolean $triggerDoi
     *  If true, a DOI mailing will be triggered.
     * @param string $doiMailingKey
     *  If this parameter is set, the DOI mailing with the given ID will be triggered.
     *  If not set, the default DOI Mailing will be triggered.
     * @param string $ignoreChecksum
     *  If this is true, the checksum will not be validated.
     *  This is only valid if the request is NOT triggered by the contact (e.g. on a profile change landing page)
     *  but from a third party system.
     * @return \em MaileonAPIResult
     *  the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function updateContact(
        $contact,
        $checksum = "",
        $src = null,
        $subscriptionPage = null,
        $triggerDoi = false,
        $doiMailingKey = null,
        $ignoreChecksum = false
    ) {
        $queryParameters = array(
            'id' => $contact->id,
            'checksum' => $checksum,
            'triggerdoi' => ($triggerDoi == true) ? "true" : "false",
            'ignore_checksum' => $ignoreChecksum ? "true" : "false"
        );

        if (isset($contact->permission)) {
            $queryParameters['permission'] = $contact->permission->getCode();
        }
        if (isset($src)) {
            $queryParameters['src'] = $src;
        }
        if (isset($subscriptionPage)) {
            $queryParameters['page_key'] = $subscriptionPage;
        }
        $doiMailingKey = trim($doiMailingKey);
        if (!empty($doiMailingKey)) {
            $queryParameters['doimailing'] = $doiMailingKey;
        }

        // The API allows only some of the fields to be submitted
        $contactToSend = new Contact(
            null,
            $contact->email,
            null,
            $contact->external_id,
            null,
            $contact->standard_fields,
            $contact->custom_fields
        );

        return $this->put("contacts/contact", $contactToSend->toXMLString(), $queryParameters);
    }

    /**
     * Synchronizes a list of contacts with the contacts in the account and returns a
     * detailed report with stats and validation errors.
     *
     * @param Contacts $contacts
     *  the contacts to synchronize
     * @param Permission $permission
     *  the permission to set for the contacts
     * @param SynchronizationMode $syncMode
     *  the sync mode to use
     * @param string $useExternalId
     *  if set to true, the external id is used as identifier for the contacts.
     *  Otherwise the email address is used as identifier.
     * @param string $ignoreInvalidContacts
     *  if set to true, invalid contacts are ignored and the synchronization
     *  succeeds for valid contacts.
     * @param string $reimportUnsubscribedContacts
     *  if set to true, unsubscribed contacts will be imported, if false, they will be ommitted
     * @param boolean $overridePermission
     *  if set to true the permission of existing and non existing contacts will be overwridden,
     *  if false, the permission will be used for new contacts only and existing contacts will not be influenced.
     * @param boolean $updateOnly
     *  If true, only existing contacts are updated and no new contacts are created
     * @param boolean $preferMaileonId
     *  If true, Maileon tries identifying contacts by Maileon-ID, if available. Fallback is always the email address.
     * @return \em MaileonAPIResult
     *    the result object of the API call. The
     *  response XML reports which contacts were successfully synchronized as well as any errors that
     *  might have occurred.
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function synchronizeContacts(
        $contacts,
        $permission = null,
        $syncMode = null,
        $useExternalId = false,
        $ignoreInvalidContacts = false,
        $reimportUnsubscribedContacts = true,
        $overridePermission = true,
        $updateOnly = false,
        $preferMaileonId = false
    ) {
        $queryParameters = array(
            'permission' => ($permission == null) ? 1 : $permission->getCode(),
            'sync_mode' => ($syncMode == null) ? 2 : $syncMode->getCode(),
            'use_external_id' => ($useExternalId == true) ? "true" : "false",
            'ignore_invalid_contacts' => ($ignoreInvalidContacts == true) ? "true" : "false",
            'reimport_unsubscribed_contacts' => ($reimportUnsubscribedContacts == true) ? "true" : "false",
            'override_permission' => ($overridePermission == true) ? "true" : "false",
            'update_only' => ($updateOnly == true) ? "true" : "false",
            'prefer_maileon_id' => ($preferMaileonId == true) ? "true" : "false"
        );

        $cleanedContacts = new Contacts();
        foreach ($contacts as $contact) {
            $cleanedContact = new Contact(
                $contact->id,
                $contact->email,
                null,
                $contact->external_id,
                null,
                $contact->standard_fields,
                $contact->custom_fields
            );
            $cleanedContacts->addContact($cleanedContact);
        }

        return $this->post("contacts", $cleanedContacts->toXMLString(), $queryParameters);
    }

    /**
     * This method unsubscribes a contact from Maileon using the contact's email adress.
     *
     * @param string $email The email address of the contact.
     * @param long $mailingId The ID of the mailing to assign the unsubscribe to.
     * The mailing must have been sent, i.e. be sealed.
     * @param array $reasons an array of reasons or a single reason (string).
     * Unsubscription reasons have two layers
     * of information, see http://dev.maileon.com/api/rest-api-1-0/contacts/unsubscribe-contacts-by-email
     * for more details about the format.
     * The parameter(s) will be url-encoded by the client, you do not need to provide urlencoded strings.
     * @param array $nlAccounts Optional parameter to define in which accounts the
     * email should be unsubscribed. Note: The accounts must belong to the owner of
     * the API key in use, otherwise they will be ignored.
     * @return \em MaileonAPIResult
     *  the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function unsubscribeContactByEmail($email, $mailingId = "", $reasons = null, $nlAccountIds = array())
    {
        $queryParameters = array();
        if (!empty($mailingId)) {
            $queryParameters['mailingId'] = $mailingId;
        }

        if (!empty($reasons)) {
            if (is_array($reasons)) {
                $queryParameters = $this->appendArrayFields($queryParameters, 'reason', $reasons);
            } else {
                $queryParameters['reason'] = urlencode($reasons);
            }
        }

        if (!empty($nlAccountIds)) {
            if (is_array($nlAccountIds)) {
                $queryParameters = $this->appendArrayFields($queryParameters, 'nlaccountid', $nlAccountIds);
            } else {
                $queryParameters['nlaccountid'] = urlencode($nlAccountIds);
            }
        }

        $queryParameters = $this->appendArrayFields($queryParameters, "nlaccountid", $nlAccountIds);

        $encodedEmail = utf8_encode($email);
        return $this->delete("contacts/email/${encodedEmail}/unsubscribe", $queryParameters);
    }

    /**
     * This method adds unsubscription reasons to an unsubscribed contact.
     * The contact must already be unsubscribed, otherwise 400 will be returned by the PAI
     *
     * @param int $id The ID of the contact.
     * @param string $checksum The checksum generated by Maileon
     * @param array $reasons an array of reasons or a single reason (string).
     * Unsubscription reasons have two layers
     * of information, see http://dev.maileon.com/api/rest-api-1-0/contacts/unsubscribe-contacts-by-email
     * for more details about the format.
     * The parameter(s) will be url-encoded by the client, you do not need to provide urlencoded strings.
     * @param boolean $ignore_checksum If the call comes from an authorized
     * system instead of the user you might ignore the checksum
     * @return \em MaileonAPIResult
     *  the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function addUnsubscriptionReasonsToUnsubscribedContact(
        $id,
        $checksum = null,
        $reasons = null,
        $ignore_checksum = false
    ) {
        $queryParameters = array();
        $queryParameters['id'] = $id;
        if (!empty($checksum)) {
            $queryParameters['checksum'] = $checksum;
        }
        if ($ignore_checksum===true) {
            $queryParameters['ignore_checksum'] = "true";
        }

        if (!empty($reasons)) {
            if (is_array($reasons)) {
                $queryParameters = $this->appendArrayFields($queryParameters, 'reason', $reasons);
            } else {
                $queryParameters['reason'] = urlencode($reasons);
            }
        }

        return $this->put("contacts/contact/unsubscribe/reasons", null, $queryParameters);
    }

    /**
     * This method unsubscribes a contact from Maileon using the Maileon id.
     *
     * @param int $id
     * @param long $mailingId The ID of the mailing to assign the unsubscribe to.
     * The mailing must have been sent, i.e. be sealed.
     * @param array $reasons an array of reasons or a single reason (string).
     * Unsubscription reasons have two layers
     * of information, see http://dev.maileon.com/api/rest-api-1-0/contacts/unsubscribe-contacts-by-maileon-id
     * for more details about the format.
     * The parameter(s) will be url-encoded by the client, you do not need to provide urlencoded strings.
     * @return \em MaileonAPIResult
     *  the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function unsubscribeContactById($id, $mailingId = "", $reasons = null)
    {
        $queryParameters = array(
            'id' => $id
        );

        if (!empty($mailingId)) {
            $queryParameters['mailingId'] = $mailingId;
        }
        if (!empty($reasons)) {
            if (is_array($reasons)) {
                $queryParameters = $this->appendArrayFields($queryParameters, 'reason', $reasons);
            } else {
                $queryParameters['reason'] = urlencode($reasons);
            }
        }

        return $this->delete("contacts/contact/unsubscribe", $queryParameters);
    }

    /**
     * This method unsubscribes a contact from Maileon using the external id.
     *
     * @param string $externalId The external ID of the contact.
     * @param long $mailingId The ID of the mailing to assign the unsubscribe to.
     * The mailing must have been sent, i.e. be sealed.
     * @param array $reasons an array of reasons or a single reason (string).
     * Unsubscription reasons have two layers
     * of information, see http://dev.maileon.com/api/rest-api-1-0/contacts/unsubscribe-contacts-external-id
     * for more details about the format.
     * The parameter(s) will be url-encoded by the client, you do not need to provide urlencoded strings.
     * @return \em MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function unsubscribeContactByExternalId($externalId, $mailingId = "", $reasons = null)
    {
        $queryParameters = array();
        if (!empty($mailingId)) {
            $queryParameters['mailingId'] = $mailingId;
        }

        if (!empty($reasons)) {
            if (is_array($reasons)) {
                $queryParameters = $this->appendArrayFields($queryParameters, 'reason', $reasons);
            } else {
                $queryParameters['reason'] = urlencode($reasons);
            }
        }

        $encodedExternalId = utf8_encode($externalId);
        return $this->delete("contacts/externalid/${encodedExternalId}/unsubscribe", $queryParameters);
    }

    /**
     * This method unsubscribes a contact from Maileon from several accounts
     * (owner of API key must also be the same customer owning the other accounts).
     *
     * @param int $externalId
     * @param long $nlaccountid The ID of the mailing to assign the unsubscribe to.
     * The mailing must have been sent, i.e. be sealed.
     * @return \em MaileonAPIResult
     *  the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function unsubscribeContactByExternalIdFromMultipleAccounts($externalId, $nlAccountIds = array())
    {
        $queryParameters = array();
        $queryParameters = $this->appendArrayFields($queryParameters, "nlaccountid", $nlAccountIds);

        $encodedExternalId = utf8_encode($externalId);
        return $this->delete("contacts/externalid/${encodedExternalId}/unsubscribe", $queryParameters);
    }

    /**
     * Returns a page of blocked contacts. Blocked contacts are contacts with available permission
     * but that are blocked for sendouts because of blacklist matches or similar reasons such as
     * bounce policy.
     *
     * @param string[] $standardFields
     *  the standard fields to select
     * @param string[] $customFields
     *  the custom fields to select
     * @param number $pageIndex
     *  the paging index of the page to retrieve
     * @param number $pageSize
     *  the number of results per page
     * @return \em MaileonAPIResult
     *    the result object of the API call
     * @return \em MaileonAPIResult
     *    the result object of the API call, with a com_maileon_api_co
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurredntacts_Contacts
     *  available at MaileonAPIResult::getResult()
     */
    public function getBlockedContacts(
        $standardFields = array(),
        $customFields = array(),
        $pageIndex = 1,
        $pageSize = 1000
    ) {
        $queryParameters = array(
            'standard_field' => $standardFields,
            'page_index' => $pageIndex,
            'page_size' => $pageSize
        );

        $queryParameters = $this->appendArrayFields($queryParameters, 'custom_field', $customFields);

        return $this->get('contacts/blocked', $queryParameters);
    }

    /**
     * This method removes a contact completely from Maileon using the maileon ID.
     * WARNING: the contact is COMPLETELY removed, not only unsubscribed. This means that not only the
     * contact data is removed but also all statistics change.
     * For most usecases the unsubscribe method is more appropriate.
     *
     * @param string $email
     *  The email address of the contact to delete. Does not need to be unique.
     * @return \em MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function deleteContact($id)
    {
        $queryParameters = array('id' => $id);
        return $this->delete("contacts/contact", $queryParameters);
    }

    /**
     * This method removes all contacts completely from Maileon using the email address.
     * WARNING: the contacts are COMPLETELY removed, not only unsubscribed. This means that not
     * only the contact data is removed but also all statistics change.
     * For most usecases the unsubscribe method is more appropriate.
     *
     * @param string $email
     *  The email address of the contact to delete. Does not need to be unique.
     * @return \em MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function deleteContactByEmail($email)
    {
        return $this->delete("contacts/email/" . utf8_encode($email));
    }

    /**
     * This method removes the contacts completely from Maileon using the external id.
     * WARNING: the contacts are COMPLETELY removed, not only unsubscribed. This means that not only
     * the contact data is removed but also all statistics change.
     * For most usecases the unsubscribe method is more appropriate.
     *
     * @param string $external ID
     *  The external ID of the contact to delete. Does not need to be unique.
     * @return \em MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function deleteContactsByExternalId($externalId)
    {
        return $this->delete("contacts/externalid/" . utf8_encode($externalId));
    }

    /**
     * This method DELETES ALL CONTACTS. Never ever call this unless you'd prefer a career change anyway.
     *
     * @return \em MaileonAPIResult
     *  the result object of the API call, with a Contact
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function deleteAllContacts($authorized = "no")
    {
        $queryParameters = array(
            'authorized' => $authorized
        );
        return $this->delete("contacts", $queryParameters);
    }

    /**
     * Creates a custom contact field with the provided name and data type.
     *
     * @param string $name
     *  the name of the new field
     * @param string $type
     *  the type of the new field. Valid values are 'string', 'integer', 'float', 'date' or 'boolean'.
     * @return \em MaileonAPIResult
     *  the result object of the API call, with a Contact
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function createCustomField($name, $type = 'string')
    {
        $queryParameters = array('type' => $type);
        $encodedName = rawurlencode(mb_convert_encoding($name, "UTF-8"));
        return $this->post("contacts/fields/custom/${encodedName}", "", $queryParameters);
    }

    /**
     * Returns the custom contact fields defined in the account.
     *
     * @return \em MaileonAPIResult
     *    the result object of the API call, with a com_maileon_api_contacts_CustomFields
     *  available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function getCustomFields()
    {
        return $this->get('contacts/fields/custom');
    }

    /**
     * Renames a custom contact field. The data type and the recorded values for
     * the contacts remain unchanged.
     *
     * @param string $oldName
     *  the current name of the field
     * @param string $newName
     *  the new name of the field
     * @return \em MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function renameCustomField($oldName, $newName)
    {
        $encodedOldName = rawurlencode(mb_convert_encoding($oldName, "UTF-8"));
        $encodedNewName = rawurlencode(mb_convert_encoding($newName, "UTF-8"));
        return $this->put("contacts/fields/custom/${encodedOldName}/${encodedNewName}");
    }

    /**
     * Deletes the custom contact field with the provided name. Note that all the values of the
     * field get auotmatically deleted by this call.
     *
     * @param string $name
     *  the name of the field to delete
     * @return \em MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function deleteCustomField($name)
    {
        $encodedName = rawurlencode(mb_convert_encoding($name, "UTF-8"));
        return $this->delete("contacts/fields/custom/{$encodedName}");
    }

    /**
     * Deletes the values of the given standard contact field for all contacts.
     *
     * @param string $name
     *  the name of the field whose values to delete
     * @return \em MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function deleteStandardFieldValues($name)
    {
        $encodedName = urlencode(mb_convert_encoding($name, "UTF-8"));
        return $this->delete("contacts/fields/standard/{$encodedName}/values");
    }

    /**
     * Deletes the values of the given custom contact field for all contacts.
     *
     * @param string $name
     *  the name of the field whose values to delete
     * @return \em MaileonAPIResult
     *    the result object of the API call
     * @throws MaileonAPIException
     *  if there was a connection problem or a server error occurred
     */
    public function deleteCustomFieldValues($name)
    {
        $encodedName = rawurlencode(mb_convert_encoding($name, "UTF-8"));
        return $this->delete("contacts/fields/custom/{$encodedName}/values");
    }
}
