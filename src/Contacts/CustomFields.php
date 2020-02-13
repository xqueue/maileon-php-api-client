<?php

namespace Maileon\Contacts;

use Maileon\Xml\AbstractXMLWrapper;

/**
 * The wrapper class for a list of custom fields.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class CustomFields extends AbstractXMLWrapper
{

    public $custom_fields;

    /**
     * Constructor initializing the list from an array.
     *
     * @param array $custom_fields
     *  The list of custom fields. Empty array if no argument passed.
     */
    public function __construct($custom_fields = array())
    {
        $this->custom_fields = $custom_fields;
    }

    /**
     * Initialization of the custom fields from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the custom fields from.
     */
    public function fromXML($xmlElement)
    {
        foreach ($xmlElement->children() as $field) {
            $this->custom_fields[trim($field->name)] = $field->type;
            // The trim is required to make a safer string from the object
        }
    }

    /**
     * Serialization to a simple XML element.
     *
     * @return \em SimpleXMLElement
     *  Generate a XML element from the custom fields list.
     */
    public function toXML()
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><custom_fields></custom_fields>");
        if (isset($this->custom_fields)) {
            foreach ($this->custom_fields as $index => $type) {
                $field = $xml->addChild("field");
                $field->addChild("name", $index);
                $field->addChild("type", $type);
            }
        }

        return $xml;
    }

    /**
     * Serialization to a simple XML element as string
     *
     * @return \em string
     *  The string representation of the XML document for this list of custom fields.
     */
    public function toXMLString()
    {
        $xml = $this->toXML();
        return $xml->asXML();
    }

    /**
     * Human readable representation of this list of custom fields.
     *
     * @return \em string
     *  The human readable representation of the list of custom fields.
     */
    public function toString()
    {

        // Generate custom field string
        $customfields = "";
        if (isset($this->custom_fields)) {
            foreach ($this->custom_fields as $index => $type) {
                $customfields .= $index . "=" . $type . ", ";
            }
            $customfields = rtrim($customfields, ', ');
        }

        return "CustomFields = {" . $customfields . "}";
    }
}
