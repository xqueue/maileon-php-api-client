<?php

namespace de\xqueue\maileon\api\client\account;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use SimpleXMLElement;

use function dom_import_simplexml;

/**
 * The wrapper class for a Maileon account placeholder. This class wraps the XML structure.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class AccountPlaceholder extends AbstractXMLWrapper
{
    public $key;
    public $value;

    /**
     * Constructor initializing default values.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct(
        $key = null,
        $value = null
    ) {
        $this->key   = $key;
        $this->value = $value;
    }

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->key)) {
            $this->key = (string) $xmlElement->key;
        }

        if (isset($xmlElement->value)) {
            $this->value = (string) $xmlElement->value;
        }
    }

    public function toXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><account_placeholder></account_placeholder>');

        // $xml->addChild('value', $this->value);

        $xml->addChild('key', $this->key);

        // Add value as CDATA as it can contain special characters
        $xml->value = null;
        $node       = dom_import_simplexml($xml->value);
        $no         = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($this->value));

        return $xml;
    }

    public function toString(): string
    {
        return 'AccountPlaceholder ['
            . 'key=' . $this->key
            . ', value=' . $this->value
            . ']';
    }
}
