<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\xml\XMLDeserializer;

use function trim;

/**
 * This class represents a contact in the reporting. Such a contact not only contains the contact
 * properties but also the field backups (at sending time).
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class ReportContact extends Contact
{

    /**
     * Field Backups are the values of contact fields that have been backed up for mailings
     * because of a backup instruction. Note that this only applies for non anonymizable field backups.
     *
     * FieldBackup[]
     *
     * @var array
     */
    public $fieldBackups = [];

    /**
     * Constructor for initializing a Report Contact from a given contact
     *
     * @param Contact $contact
     */
    public function __construct($contact = null)
    {
        // parent::__construct() ?

        if (isset($contact)) {
            $this->anonymous       = $contact->anonymous;
            $this->email           = $contact->email;
            $this->external_id     = $contact->external_id;
            $this->id              = $contact->id;
            $this->created         = $contact->created;
            $this->updated         = $contact->updated;
            $this->permission      = $contact->permission;
            $this->standard_fields = $contact->standard_fields;
            $this->custom_fields   = $contact->custom_fields;
        }
    }

    public function fromXML($xmlElement)
    {
        parent::fromXML($xmlElement);

        if (isset($xmlElement->permissionType)) {
            $this->permission = Permission::getPermission((int) $xmlElement->permissionType);
        }

        if (isset($xmlElement->standard_fields)) {
            $this->standard_fields = [];

            foreach ($xmlElement->standard_fields->children() as $field) {
                $this->standard_fields[trim($field->name)] = (string) $field->value;
                // The trim is required to make a safer string from the object
            }
        }

        if (isset($xmlElement->custom_fields)) {
            foreach ($xmlElement->custom_fields->children() as $field) {
                $this->custom_fields[trim($field->name)] = (string) $field->value;
                // The trim is required to make a safer string from the object
            }
        }

        if (isset($xmlElement->field_backups)) {
            $this->fieldBackups = XMLDeserializer::deserialize($xmlElement->field_backups);
        }
    }
}
