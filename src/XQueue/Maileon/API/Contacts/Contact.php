<?php

namespace XQueue\Maileon\API\Contacts;

use XQueue\Maileon\API\Contacts\Permission;
use XQueue\Maileon\API\XML\AbstractXMLWrapper;
use XQueue\Maileon\API\XML\XMLUtils;

/**
 * The wrapper class for a Maileon contact. This class wraps the XML structure.
 *
 * @author Felix Heinrichs | Trusted Mails GmbH | <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Contact extends AbstractXMLWrapper
{
    public $id;
    public $email;
    public $permission;
    public $external_id;
    public $anonymous;
    public $created;
    public $updated;
    public $standard_fields;
    public $custom_fields;

    /**
     * Constructor initializing default values.
     *
     * @param number $id
     *  The Maileon contact id.
     * @param string $email
     *  The email-address of the contact.
     * @param string $permission
     *  The permission code. 1 = NONE, 2 = SOI, 3 = COI, 4 = DOI, 5 = DOI+, 6 = OTHER.
     * @param string $external_id
     *  The external id to identify the contact.
     * @param boolean $anonymous
     * @param array $standard_fields
     *  An array of standard fields.
     * @param array $custom_fields
     *  An array of custom fields of the contact.
     */
    function __construct(
        $id = null,
        $email = null,
        $permission = NULL,
        $external_id = -1,
        $anonymous = false,
        $standard_fields = array(),
        $custom_fields = array(),
        $created = null,
        $updated = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->permission = $permission;
        $this->external_id = $external_id;
        $this->anonymous = $anonymous;
        $this->standard_fields = $standard_fields;
        $this->custom_fields = $custom_fields;
        $this->created = $created;
        $this->updated = $updated;
    }

    /**
     * Initialization of the contact from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the contact from.
     */
    function fromXML($xmlElement)
    {

        if (isset($xmlElement->id)) $this->id = $xmlElement->id;
        $this->email = (string)$xmlElement->email;
        if (isset($xmlElement->permission)) $this->permission = Permission::getPermission((string)$xmlElement->permission);
        if (isset($xmlElement->external_id)) (string)$this->external_id = $xmlElement->external_id;
        if (isset($xmlElement->anonymous)) (string)$this->anonymous = $xmlElement->anonymous;
        if (isset($xmlElement['anonymous'])) $this->anonymous = $xmlElement['anonymous'];

        if (isset($xmlElement->created)) $this->created = $xmlElement->created;
        if (isset($xmlElement->updated)) $this->updated = $xmlElement->updated;

        if (isset($xmlElement->standard_fields)) {
            $this->standard_fields = array();
            foreach ($xmlElement->standard_fields->children() as $field) {
                $this->standard_fields[trim($field->name)] = (string)$field->value; // The trim is required to make a safer string from the object
            }
        }

        if (isset($xmlElement->custom_fields)) {
            foreach ($xmlElement->custom_fields->children() as $field) {
                $this->custom_fields[trim($field->name)] = (string)$field->value; // The trim is required to make a safer string from the object
            }
        }
    }

    /**
     * Serialization to a simple XML element.
     *
     * @param bool $addXMLDeclaration
     *
     * @return \em \SimpleXMLElement
     *  Generate a XML element from the contact object.
     */
    function toXML($addXMLDeclaration = true)
    {
        $xmlString = $addXMLDeclaration ? "<?xml version=\"1.0\"?><contact></contact>" : "<contact></contact>";
        $xml = new \SimpleXMLElement($xmlString);

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->id)) $xml->addChild("id", $this->id);

        // As shown in http://stackoverflow.com/questions/17027043/unterminated-entity-reference-php a & char causes trouble with addChild.
        // Use this workaround
        if (isset($this->email)) {
            $emailChild = $xml->addChild("email");
            $xml->email = $this->email;
        }

        if (isset($this->permission)) $xml->addChild("permission", $this->permission->getCode());
        if (isset($this->external_id) && $this->external_id != -1) $xml->addChild("external_id", $this->external_id);
        if (isset($this->anonymous)) $xml->addChild("anonymous", $this->anonymous);

        if (isset($this->created)) $xml->addChild("created", $this->created);
        if (isset($this->updated)) $xml->addChild("updated", $this->updated);

        if (isset($this->standard_fields)) {
            $standard_fields = $xml->addChild("standard_fields");
            foreach ($this->standard_fields as $index => $value) {
                $field = $standard_fields->addChild("field");
                $field->addChild("name", $index);

                XMLUtils::addChildAsCDATA($field, "value", $value);
                //$field->addChild("value", $value);
            }
        }

        if (isset($this->custom_fields)) {
            $customfields = $xml->addChild("custom_fields");
            foreach ($this->custom_fields as $index => $value) {
                $field = $customfields->addChild("field");
                $field->addChild("name", $index);

                XMLUtils::addChildAsCDATA($field, "value", $value);
                //$field->addChild("value", $value);
            }
        }

        return $xml;
    }

    /**
     * Serialization to a simple XML element as string
     *
     * @return \em string
     *  The string representation of the XML document for this contact.
     */
    function toXMLString()
    {
        $xml = $this->toXML();
        return $xml->asXML();
    }

    /**
     * Human readable representation of this wrapper.
     *
     * @return \em string
     *  A human readable version of the contact.
     */
    function __toString()
    {

        // Generate standard field string
        $standard_fields = "";
        if (isset($this->standard_fields)) {
            foreach ($this->standard_fields as $index => $value) {
                $standard_fields .= $index . "=" . $value . ",";
            }
            $standard_fields = rtrim($standard_fields, ',');
        }

        // Generate custom field string
        $customfields = "";
        if (isset($this->custom_fields)) {
            foreach ($this->custom_fields as $index => $value) {
                $customfields .= $index . "=" . $value . ",";
            }
            $customfields = rtrim($customfields, ',');
        }

        $permission = "";
        if (isset($this->permission)) {
            $permission = $this->permission->getCode();
        }

        return "Contact [id=" . $this->id . ", email="
        . $this->email . ", permission=" . $permission . ", external_id=" . $this->external_id
        . ", anonymous=" . (($this->anonymous == true) ? "true" : "false") . ", created=" . $this->created . ", updated=" . $this->updated
        . ", standard_fields={" . $standard_fields . "}, customfields={" . $customfields . "}]";
    }

    /**
     * CSV representation of this wrapper.
     *
     * @return \em string
     *  A csv version of the contact.
     */
    function toCsvString()
    {

        // Generate standard field string
        $standard_fields = "{";
        if (isset($this->standard_fields)) {
            foreach ($this->standard_fields as $index => $value) {
                $standard_fields .= $index . "=" . $value . ",";
            }
            $standard_fields = rtrim($standard_fields, ',');
        }
        $standard_fields .= "}";

        // Generate custom field string
        $customfields = "{";
        if (isset($this->custom_fields)) {
            foreach ($this->custom_fields as $index => $value) {
                $customfields .= $index . "=" . $value . ",";
            }
            $customfields = rtrim($customfields, ',');
        }
        $customfields .= "}";

        $permission = "";
        if (isset($this->permission)) {
            $permission = $this->permission->getCode();
        }

        return $this->id
        . ";" . $this->email
        . ";" . $permission
        . ";" . $this->external_id
        . ";" . (($this->anonymous == true) ? "true" : "false")
        . ";" . $this->created
        . ";" . $this->updated
        . ";\"" . $standard_fields . "\""
        . ";\"" . $customfields . "\"";
    }
}