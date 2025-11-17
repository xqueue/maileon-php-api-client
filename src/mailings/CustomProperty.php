<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use SimpleXMLElement;

/**
 * The wrapper class for a Maileon custom property. This class wraps the XML structure.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class CustomProperty extends AbstractXMLWrapper
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
        $xml = new SimpleXMLElement('<?xml version="1.0"?><property></property>');

        $xml->addChild('key', $this->key);
        $xml->addChild('value', $this->value);

        return $xml;
    }

    public function toString(): string
    {
        return 'CustomProperty ['
            . 'key=' . $this->key
            . ', value=' . $this->value
            . ']';
    }
}
