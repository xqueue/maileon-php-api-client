<?php

namespace de\xqueue\maileon\api\client\blacklists;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for a blacklist.
 */
class Blacklist extends AbstractXMLWrapper
{
    /**
     * @var integer
     *        the id of the blacklist
     */
    public $id;

    /**
     * @var string
     *        the name of the blacklist
     */
    public $name;

    /**
     * @var string[]
     *        the blacklist entries
     */
    public $entries;

    /**
     * @return string
     *         a human-readable representation of this blacklist.
     */
    public function toString()
    {
        return "Blacklist [id=" . $this->id . ", name=" . $this->name . ", entries=[" .
            (is_array($this->entries) ? implode(", ", $this->entries) : "") . "]]";
    }

    /**
     * Initializes this blacklist type from an XML representation.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the serialized XML representation to use
     */
    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) {
            $this->id = $xmlElement->id;
        }
        if (isset($xmlElement->name)) {
            $this->name = $xmlElement->name;
        }

        if (isset($xmlElement->entries)) {
            $this->entries = array();
            foreach ($xmlElement->entries->children() as $entry) {
                $this->entries[] = $entry;
            }
        }
    }

    public function toXML()
    {
        $xmlString = "<?xml version=\"1.0\"?><blacklist></blacklist>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->id)) {
            $xml->addChild("id", $this->id);
        }
        if (isset($this->name)) {
            $xml->addChild("name", $this->name);
        }

        if (isset($this->entries)) {
            $entries = $xml->addChild("entries", 'entries');
            foreach ($this->entries as $entry) {
                $entries->addChild($entry, $entry);
            }
        }

        return $xml;
    }
}
