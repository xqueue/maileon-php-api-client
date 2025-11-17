<?php

namespace de\xqueue\maileon\api\client\blacklists;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use SimpleXMLElement;

use function implode;
use function is_array;

/**
 * The wrapper class for a Blacklist import.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class AddEntriesAction extends AbstractXMLWrapper
{
    /**
     * the name of the blacklist import
     *
     * @var string
     */
    public $importName;

    /**
     * the blacklist entries to add
     *
     * @var string[]
     */
    public $entries;

    public function fromXML($xmlElement)
    {
        // not implemented
    }

    public function toXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><add_entries_action></add_entries_action>');

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->importName)) {
            $xml->addChild('import_name', $this->importName);
        }

        if (is_array($this->entries)) {
            $entries = $xml->addChild('entries');

            foreach ($this->entries as $entry) {
                $entries->addChild('entry', $entry);
            }
        }

        return $xml;
    }

    public function toString(): string
    {
        return 'AddEntriesAction ['
            . 'importName=' . $this->importName
            . ', entries=[' . (is_array($this->entries) ? implode(', ', $this->entries) : '') . ']'
            . ']';
    }
}
