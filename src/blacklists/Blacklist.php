<?php

namespace de\xqueue\maileon\api\client\blacklists;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use SimpleXMLElement;

use function implode;
use function is_array;

/**
 * The wrapper class for a blacklist.
 */
class Blacklist extends AbstractXMLWrapper
{
    /**
     * the id of the blacklist
     *
     * @var int
     */
    public $id;

    /**
     * the name of the blacklist
     *
     * @var string
     */
    public $name;

    /**
     * the blacklist entries
     *
     * @var string[]
     */
    public $entries;

    public function toString(): string
    {
        return 'Blacklist ['
            . 'id=' . $this->id
            . ', name=' . $this->name
            . ', entries=[' . (is_array($this->entries) ? implode(', ', $this->entries) : '') . ']'
            . ']';
    }

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) {
            $this->id = $xmlElement->id;
        }

        if (isset($xmlElement->name)) {
            $this->name = $xmlElement->name;
        }

        if (isset($xmlElement->entries)) {
            $this->entries = [];

            foreach ($xmlElement->entries->children() as $entry) {
                $this->entries[] = $entry;
            }
        }
    }

    public function toXML()
    {
        $xmlString = '<?xml version="1.0"?><blacklist></blacklist>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->id)) {
            $xml->addChild('id', $this->id);
        }

        if (isset($this->name)) {
            $xml->addChild('name', $this->name);
        }

        if (isset($this->entries)) {
            $entries = $xml->addChild('entries', 'entries');

            foreach ($this->entries as $entry) {
                $entries->addChild($entry, $entry);
            }
        }

        return $xml;
    }
}
