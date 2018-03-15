<?php

namespace XQueue\Maileon\API\Blacklists;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;

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
     * @return \em string
     *         a human-readable representation of this blacklist.
     */
    function toString()
    {
        return "Blacklist [id=" . $this->id . ", name=" . $this->id . ", entries=[" . (is_array($this->entries) ? implode(", ", $this->entries) : "") . "]]";
    }

    /**
     * Initializes this blacklist type from an XML representation.
     *
     * @param SimpleXMLElement $xmlElement
     *  the serialized XML representation to use
     */
    function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) $this->id = $xmlElement->id;
        if (isset($xmlElement->name)) $this->name = $xmlElement->name;

        if (isset($xmlElement->entries)) {
            $this->entries = array();
            foreach ($xmlElement->entries->children() as $entry) {
                $this->entries[] = $entry;
            }
        }
    }

    function toXML()
    {
        // not supported
    }
}