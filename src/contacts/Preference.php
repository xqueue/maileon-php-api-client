<?php

namespace de\xqueue\maileon\api\client\contacts;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for a Maileon Preference Category. This class wraps the XML structure.
 */
class Preference extends AbstractXMLWrapper
{
    public $name;
    public $description;
    public $category;
    public $value;
    public $source;
    public $last_modified;

    /**
     * Constructor initializing default values.
     *
     * @param string $name
     *  The preference category name.
     * @param string $description
     *  The preference category description.
     */
    public function __construct(
        $name = null,
        $description = null,
        $category = null,
        $value = null,
        $source = null,
        $last_modified = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->category = $category;
        $this->value = $value;
        $this->source = $source;
        $this->last_modified = $last_modified;
    }

    /**
     * Initialization of the preference category from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the preference category from.
     */
    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->name)) {
            $this->name = $xmlElement->name;
        }
        
        if (isset($xmlElement->description)) {
            $this->description = $xmlElement->description;
        }

        if (isset($xmlElement->category)) {
            $this->category = $xmlElement->category;
        }

        if (isset($xmlElement->value)) {
            $this->value = $xmlElement->value;
        }

        if (isset($xmlElement->source)) {
            $this->source = $xmlElement->source;
        }

        if (isset($xmlElement->last_modified)) {
            $this->last_modified = $xmlElement->last_modified;
        }
    }

    /**
     * Serialization to a simple XML element.
     *
     * @param bool $addXMLDeclaration
     *
     * @return \SimpleXMLElement
     * Generate a XML element from the preference category object.
     */
    public function toXML($addXMLDeclaration = true)
    {
        if ($addXMLDeclaration) {
            $xmlString = "<?xml version=\"1.0\"?><preference></preference>";
        } else {
            $xmlString = "<preference></preference>";
        }

        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->name)) {
            $xml->addChild("name", $this->name);
        }
        if (isset($this->description)) {
            $xml->addChild("description", $this->description);
        }
        if (isset($this->category)) {
            $xml->addChild("category", $this->category);
        }
        if (isset($this->value)) {
            $xml->addChild("value", $this->value);
        }
        if (isset($this->source)) {
            $xml->addChild("source", $this->source);
        }
        if (isset($this->last_modified)) {
            $xml->addChild("last_modified", $this->last_modified);
        }

        return $xml;
    }

    /**
     * Serialization to a simple XML element as string
     *
     * @return string
     *  The string representation of the XML document for this preference category.
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
     *  A human readable version of the preference category.
     */
    public function toString()
    {
        return "Contact Preference [name=" . $this->name .
        ", description=" . $this->description .
        ", category=" . $this->category .
        ", value=" . $this->value .
        ", source=" . $this->source .
        ", last_modified=" . $this->last_modified .
        "]";
    }

    /**
     * CSV representation of this wrapper.
     *
     * @return string
     *  A csv version of the category reference.
     */
    public function toCsvString()
    {
        return $this->name .
        ";" . $this->description .
        ";" . $this->category .
        ";" . $this->value .
        ";" . $this->source .
        ";" . $this->last_modified;
    }
}
