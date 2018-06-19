<?php

namespace XQueue\Maileon\API\Mailings;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;
use XQueue\Maileon\API\XML\XMLUtils;

/**
 * The wrapper class for a Maileon mailing. This class wraps the XML structure.
 *
 * @author Marcus St&auml;nder | Trusted Technologies GmbH | <a href="mailto:marcus.staender@trusted-technologies.de">marcus.staender@trusted-technologies.de</a>
 */
class Mailing extends AbstractXMLWrapper
{
    public $id;
    public $fields;

    /**
     * Constructor initializing default values.
     *
     * @param number $id
     *  The Maileon mailing id.
     * @param array $fields
     *  An array of fields.
     */
    function __construct(
        $id = null,
        $fields = array())
    {
        $this->id = $id;
        $this->fields = $fields;
    }

    /**
     * Initialization of the mailing from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the mailing from.
     */
    function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) $this->id = $xmlElement->id;

        if (isset($xmlElement->fields)) {
            $this->fields = array();
            foreach ($xmlElement->fields->children() as $field) {
                $this->fields[trim($field->name)] = (string)$field->value; // The trim is required to make a safer string from the object
            }
        }
    }

    /**
     * Returns the value of the field with the given name
     *
     * @param string fieldName
     *  The field name of the element to return the value of
     *
     * @return \em string
     *  The value or undefined, if not found
     */
    function getFieldValue($fieldName)
    {
        $name = trim($fieldName);
        if (isset($this->fields)) {
            return ($this->fields[$name]);
        }
        return;
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
        $xmlString = $addXMLDeclaration ? "<?xml version=\"1.0\"?><mailing></mailing>" : "<mailing></mailing>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->id)) $xml->addChild("id", $this->id);

        if (isset($this->fields)) {
            $standard_fields = $xml->addChild("fields");
            foreach ($this->fields as $index => $value) {
                $field = $standard_fields->addChild("field");
                $field->addChild("name", $index);

                XMLUtils::addChildAsCDATA($field, "value", $value);
            }
        }

        return $xml;
    }

    /**
     * Serialization to a simple XML element as string
     *
     * @return \em string
     *  The string representation of the XML document for this mailing.
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
     *  A human readable version of the mailing.
     */
    function toString()
    {

        // Generate standard field string
        $fields = "";
        if (isset($this->fields)) {
            foreach ($this->fields as $index => $value) {
                $fields .= $index . "=" . $value . ",";
            }
            $fields = rtrim($fields, ',');
        }

        return "Mailing [id=" . $this->id . ", fields={" . $fields . "}]";
    }
}