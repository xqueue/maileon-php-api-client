<?php

namespace XQueue\Maileon\API\ContactEvents;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;
use XQueue\Maileon\API\ContactEvents\DataType;

/**
 * Wrapper class for Maileon contact event types.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class ContactEventType extends AbstractXMLWrapper
{
    public $id;
    public $name;
    public $active;
    public $anonymizable;
    public $description;
    public $created;
    public $updated;

    public $attributes;

    // TODO verify meaning and types of arguments
    /**
     * Creates a new contact event type object.
     *
     * @param string $id
     *    the ID of the contact event type
     * @param string $name
     *  the name of the contact event type
     * @param string $active
     * @param string $anonymizable
     * @param string $description
     * @param string $created
     * @param string $updated
     * @param unknown $attributes
     */
    function __construct(
        $id = null,
        $name = null,
        $active = null,
        $anonymizable = null,
        $description = null,
        $created = null,
        $updated = null,
        $attributes = array())
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
        $this->anonymizable = $anonymizable;
        $this->description = $description;
        $this->created = $created;
        $this->updated = $updated;
        $this->attributes = $attributes;
    }

    /**
     * Initializes this contact event type from an XML representation.
     *
     * @param SimpleXMLElement $xmlElement
     *  the serialized XML representation to use
     */
    function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) $this->id = $xmlElement->id;
        if (isset($xmlElement->name)) $this->name = $xmlElement->name;
        if (isset($xmlElement->active)) $this->active = $xmlElement->active;
        if (isset($xmlElement->anonymizable)) $this->anonymizable = $xmlElement->anonymizable;
        if (isset($xmlElement->description)) $this->description = $xmlElement->description;
        if (isset($xmlElement->created)) $this->created = $xmlElement->created;
        if (isset($xmlElement->updated)) $this->updated = $xmlElement->updated;

        if (isset($xmlElement->attributes)) {
            $this->attributes = array();
            foreach ($xmlElement->attributes->children() as $xmlAttribute) {
                $attribute = array();
                if (isset($xmlAttribute->name)) $attribute['name'] = trim($xmlAttribute->name);
                if (isset($xmlAttribute->datatype)) $attribute['datatype'] = DataType::getDataType($xmlAttribute->datatype);
                if (isset($xmlAttribute->description)) $attribute['description'] = trim($xmlAttribute->description);
                if (isset($xmlAttribute->required)) $attribute['required'] = $xmlAttribute->required;
                array_push($this->attributes, $attribute);
            }
        }
    }

    /**
     * @return \em string
     *  a human-readable representation of this object
     */
    function toString()
    {
        // Generate attributes string
        $attributes = "[";
        if (isset($this->attributes)) {
            foreach ($this->attributes as $index => $value) {
                $attributes .= "attribute (name=" . $value['name'] . ", datatype=" . $value['datatype']->getValue() . ", description=" . $value['description'] . ", required=" . (($value['required'] == true) ? "true" : "false") . "), ";
            }
            $attributes = rtrim($attributes, ' ');
            $attributes = rtrim($attributes, ',');
        }
        $attributes .= "]";

        return "ContactEventType [id=" . $this->id . ", name=" . $this->name . ", active=" . $this->active . ", anonymizable="
        . $this->anonymizable . ", description=" . $this->description . ", created=" . $this->created . ", updated=" . $this->updated . ", attributes=" . $attributes . "]";
    }

    /**
     * @return \em SimpleXMLElement
     *  containing the XML serialization of this object
     */
    function toXML()
    {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><contacteventtype></contacteventtype>");

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->id)) $xml->addChild("id", $this->id);
        if (isset($this->active)) $xml->addChild("active", $this->active);
        if (isset($this->anonymizable)) $xml->addChild("anonymizable", ($this->anonymizable == true) ? "true" : "false");
        if (isset($this->description)) $xml->addChild("description", $this->description);
        if (isset($this->created)) $xml->addChild("created", $this->created);
        if (isset($this->updated)) $xml->addChild("updated", $this->updated);
        if (isset($this->updated)) $xml->addChild("name", $this->name);

        if (isset($this->attributes) && sizeof($this->attributes) > 0) {

            $attributes = $xml->addChild("attributes");
            foreach ($this->attributes as $index => $value) {
                $field = $attributes->addChild("attribute");
                $field->addChild("name", $value->name);
                $field->addChild("datatype", $value->datatype->getValue());
                $field->addChild("description", $value->description);
                $field->addChild("required", ($value->required == true) ? "true" : "false");
            }
        }

        return $xml;
    }

    /**
     * @return \em string
     *  containing the XML serialization of this object
     */
    function toXMLString()
    {
        $xml = $this->toXML();
        return $xml->asXML();
    }
}