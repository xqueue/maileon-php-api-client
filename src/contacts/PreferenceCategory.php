<?php

namespace de\xqueue\maileon\api\client\contacts;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

/**
 * The wrapper class for a Maileon Preference Category. This class wraps the XML structure.
 */
class PreferenceCategory extends AbstractXMLWrapper
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * Constructor initializing default values.
     *
     * @param string $name        The preference category name.
     * @param string $description The preference category description.
     */
    public function __construct(
        $name = null,
        $description = null
    ) {
        $this->name        = $name;
        $this->description = $description;
    }

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
     * @return SimpleXMLElement contains the serialized representation of the object
     *
     * @throws Exception
     */
    public function toXML($addXMLDeclaration = true)
    {
        if ($addXMLDeclaration) {
            $xmlString = '<?xml version="1.0"?><preference_category></preference_category>';
        } else {
            $xmlString = '<preference_category></preference_category>';
        }

        $xml = new SimpleXMLElement($xmlString);

        if (isset($this->name)) {
            $xml->addChild('name', $this->name);
        }

        if (isset($this->description)) {
            $xml->addChild('description', $this->description);
        }

        return $xml;
    }

    public function toString(): string
    {
        return 'Contact Preference Category ['
            . 'name=' . $this->name
            . ', description=' . $this->description
            . ']';
    }

    /**
     * CSV representation of this wrapper.
     *
     * @return string
     */
    public function toCsvString(): string
    {
        return $this->name
            . ';' . $this->description;
    }
}
