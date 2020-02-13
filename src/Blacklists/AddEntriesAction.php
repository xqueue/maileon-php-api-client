<?php

namespace Maileon\Blacklists;

use Maileon\Xml\AbstractXMLWrapper;

/**
 * The wrapper class for a Blacklist import.
 *
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 * @author Marcus St&auml;nder | Trusted Technologies GmbH |
 * <a href="mailto:marcus.staender@trusted-technologies.de">marcus.staender@trusted-technologies.de</a>
 */
class AddEntriesAction extends AbstractXMLWrapper
{
    /**
     * @var string
     *        the name of the blacklist import
     */
    public $importName;

    /**
     * @var string[]
     *        the blacklist entries to add
     */
    public $entries;

    public function fromXML($xmlElement)
    {
        // not implemented
    }

    public function toXML()
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><add_entries_action></add_entries_action>");

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->importName)) {
            $xml->addChild("import_name", $this->importName);
        }

        if (is_array($this->entries)) {
            $entries = $xml->addChild("entries");
            foreach ($this->entries as $entry) {
                $entries->addChild("entry", $entry);
            }
        }
        return $xml;
    }

    public function toString()
    {
        return "AddEntriesAction [importName=" . $this->importName . ", entries=[" .
            (is_array($this->entries) ? implode(", ", $this->entries) : "") . "]]";
    }
}
