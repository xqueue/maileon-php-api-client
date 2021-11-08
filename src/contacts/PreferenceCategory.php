<?php

namespace de\xqueue\maileon\api\client\contacts;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for a Maileon Preference Category. This class wraps the XML structure.
 */
class PreferenceCategory extends AbstractXMLWrapper
{
    public $name;
    public $description;

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
        $description = null
    ) {
        $this->name = $name;
        $this->description = $description;
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
            $xmlString = "<?xml version=\"1.0\"?><preference_category></preference_category>";
        } else {
            $xmlString = "<preference_category></preference_category>";
        }

        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->name)) {
            $xml->addChild("name", $this->name);
        }
        if (isset($this->description)) {
            $xml->addChild("description", $this->description);
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
        return "Contact Preference Category [name=" . $this->name .
        ", description=" . $this->description . "]";
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
        ";" . $this->description;
    }
}
