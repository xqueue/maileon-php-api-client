<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\xml\XMLDeserializer;

/**
 * This class represents a contact in the reporting. Such a contact not only contains the contact
 * properties but also the field backups (at sending time).
 *
 * @author Viktor Balogh (Wiera)
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class ReportContact extends Contact
{

    /**
     * Field Backups are the values of contact fields that have been backed up for mailings
     * because of a backup instruction. Note
     * that this only applies for non anonymizable field backups.
     *
     * @var array of FieldBackup
     */
    public $fieldBackups = array();

    /**
     * Constructor for initializing a Report Contact from a given contact
     * @param Contact $ctontact
     */
    public function __construct($contact = null)
    {
        if (isset($contact)) {
            $this->anonymous = $contact->anonymous;
            $this->email = $contact->email;
            $this->external_id = $contact->external_id;
            $this->id = $contact->id;
            $this->created = $contact->created;
            $this->updated = $contact->updated;
            $this->standard_fields = $contact->standard_fields;
            $this->custom_fields = $contact->custom_fields;
        }
    }

    /**
     * Initialization of the report contact from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the contact list from.
     */
    public function fromXML($xmlElement)
    {
        parent::fromXML($xmlElement);

        if (isset($xmlElement->permissionType)) {
            $this->permission = Permission::getPermission($xmlElement->permissionType);
        }
        if (isset($xmlElement->field_backups)) {
            $this->fieldBackups = XMLDeserializer::deserialize($xmlElement->field_backups);
        }
    }
}
