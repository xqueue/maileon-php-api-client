<?php

namespace de\xqueue\maileon\api\client\contacts;

use de\xqueue\maileon\api\client\xml\XMLUtils;
use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for a Maileon contact. This class wraps the XML structure.
 *
 * @author Felix Heinrichs | Trusted Mails GmbH |
 * <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
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
    public $preferences;

    /**
     * Constructor initializing default values.
     *
     * @param number $id
     *  The Maileon contact id.
     * @param string $email
     *  The email-address of the contact.
     * @param Permission $permission
     *  The permission NONE, SOI, COI, DOI, DOI_PLUS, OTHER.
     * @param string $external_id
     *  The external id to identify the contact.
     * @param boolean $anonymous
     * @param array $standard_fields
     *  An array of standard fields.
     * @param array $custom_fields
     *  An array of custom fields of the contact.
     */
    public function __construct(
        $id = null,
        $email = null,
        $permission = null,
        $external_id = -1,
        $anonymous = false,
        $standard_fields = array(),
        $custom_fields = array(),
        $created = null,
        $updated = null,
        $preferences = array()
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->permission = $permission;
        $this->external_id = $external_id;
        $this->anonymous = $anonymous;
        $this->standard_fields = $standard_fields;
        $this->custom_fields = $custom_fields;
        $this->created = $created;
        $this->updated = $updated;
        $this->preferences = $preferences;
    }

    /**
     * Initialization of the contact from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the contact from.
     */
    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) {
            $this->id = $xmlElement->id;
        }
        $this->email = (string)$xmlElement->email;
        if (isset($xmlElement->permission)) {
            $this->permission = Permission::getPermission((string)$xmlElement->permission);
        }
        if (isset($xmlElement->external_id)) {
            (string)$this->external_id = $xmlElement->external_id;
        }
        if (isset($xmlElement->anonymous)) {
            (string)$this->anonymous = $xmlElement->anonymous;
        }
        if (isset($xmlElement['anonymous'])) {
            $this->anonymous = $xmlElement['anonymous'];
        }

        if (isset($xmlElement->created)) {
            $this->created = $xmlElement->created;
        }
        if (isset($xmlElement->updated)) {
            $this->updated = $xmlElement->updated;
        }

        if (isset($xmlElement->standard_fields)) {
            $this->standard_fields = array();
            foreach ($xmlElement->standard_fields->children() as $field) {
                $this->standard_fields[trim($field->name)] = (string)$field->value;
                // The trim is required to make a safer string from the object
            }
        }

        if (isset($xmlElement->custom_fields)) {
            foreach ($xmlElement->custom_fields->children() as $field) {
                $this->custom_fields[trim($field->name)] = (string)$field->value;
                // The trim is required to make a safer string from the object
            }
        }

        if (isset($xmlElement->preferences)) {
            $this->preferences = array();
            foreach ($xmlElement->preferences->children() as $preference) {
                $preference_obj = new Preference();
                $preference_obj->fromXML($preference);
                array_push($this->preferences, $preference_obj);
            }
        }
    }

    /**
     * Serialization to a simple XML element.
     *
     * @param bool $addXMLDeclaration
     *
     * @return \SimpleXMLElement
     * Generate a XML element from the contact object.
     */
    public function toXML($addXMLDeclaration = true)
    {
        $xmlString = $addXMLDeclaration ? "<?xml version=\"1.0\"?><contact></contact>" : "<contact></contact>";
        $xml = new \SimpleXMLElement($xmlString);

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->id)) {
            $xml->addChild("id", $this->id);
        }

        // As shown in http://stackoverflow.com/questions/17027043/unterminated-entity-reference-php
        // a & char causes trouble with addChild.
        // Use this workaround
        if (isset($this->email)) {
            $xml->email = $this->email;
        }

        if (isset($this->permission)) {
            $xml->addChild("permission", $this->permission->getCode());
        }
        if (isset($this->external_id) && $this->external_id != -1) {
            $xml->addChild("external_id", $this->external_id);
        }
        if (isset($this->anonymous)) {
            $xml->addChild("anonymous", $this->anonymous);
        }
        if (isset($this->created)) {
            $xml->addChild("created", $this->created);
        }
        if (isset($this->updated)) {
            $xml->addChild("updated", $this->updated);
        }

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

        if (isset($this->preferences)) {
            $preferences_field = $xml->addChild("preferences");
            foreach ($this->preferences as $preference) {
                $preference_field = $preferences_field->addChild("preference");
                $preference_field->addChild("name", $preference->name);
                $preference_field->addChild("category", $preference->category);
                
                XMLUtils::addChildAsCDATA($preference_field, "value", $preference->value);

                $preference_field->addChild("source", $preference->source);
            }
        }

        return $xml;
    }

    /**
     * Serialization to a simple XML element as string
     *
     * @return string
     *  The string representation of the XML document for this contact.
     */
    public function toXMLString()
    {
        $xml = $this->toXML();
        return $xml->asXML();
    }

    /**
     * Human readable representation of this wrapper.
     *
     * @return string
     *  A human readable version of the contact.
     */
    public function toString()
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

        $preferences = "";
        if (isset($this->preferences)) {
            foreach ($this->preferences as $preference) {
                $preferences .= $preference->toString() . ",";
            }
            $preferences = rtrim($preferences, ',');
        }

        $permission = "";
        if (isset($this->permission)) {
            $permission = $this->permission->getCode();
        }

        return "Contact [id=" . $this->id . ", email="
        . $this->email . ", permission=" . $permission . ", external_id=" . $this->external_id
        . ", anonymous=" . (($this->anonymous == true) ? "true" : "false") .
        ", created=" . $this->created . ", updated=" . $this->updated
        . ", standard_fields={" . $standard_fields . "}, customfields={" . $customfields .
        "}, preferences={" . $preferences . "}]";
    }

    /**
     * CSV representation of this wrapper.
     *
     * @return string
     *  A csv version of the contact.
     */
    public function toCsvString()
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

        // Generate preferences string
        $preferences = "{";
        if (isset($this->preferences)) {
            foreach ($this->preferences as $preference) {
                $preferences .= "{" . $preference->toCsvString() . "},";
            }
            $preferences = rtrim($preferences, ',');
        }
        $preferences .= "}";

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
        . ";\"" . $customfields . "\""
        . ";\"" . $preferences . "\"";
    }
}
