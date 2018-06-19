<?php

namespace XQueue\Maileon\API\Mailings;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;

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
     * @param type $key
     * @param type $value
     */
    function __construct(
        $key = null,
        $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Initialization of the attachment from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the attachment from.
     */
    function fromXML($xmlElement)
    {
        if (isset($xmlElement->key)) { $this->key = (string)$xmlElement->key; }
        if (isset($xmlElement->value)) { $this->value = (string)$xmlElement->value; }
    }

    /**
     * Creates the XML representation of an attachment
     * 
     * @return \SimpleXMLElement
     */
    function toXML() {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><property></property>");

        $xml->addChild("key", $this->key);
        $xml->addChild("value", $this->value);

        return $xml;
    }
    
    /**
     * Human readable representation of this wrapper.
     *
     * @return \em string
     *  A human readable version of the mailing.
     */
    function toString()
    {
        return "CustomProperty [key=" . $this->key . ", "
                . "value=" . $this->value . "]";
    }
}