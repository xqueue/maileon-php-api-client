<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

/**
 * This class represents an unsubscription reason containing the value and count.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class UnsubscriptionReason extends AbstractXMLWrapper
{
    /**
     * @var string
     */
    public $reason;

    /**
     * @var int
     */
    public $count;

    public function toString(): string
    {
        return 'Unsubscriber ['
            . 'reason=' . $this->reason
            . ', count=' . $this->count
            . ']';
    }

    /**
     * CSV representation of this wrapper.
     *
     * @return string
     */
    public function toCsvString(): string
    {
        return $this->reason
            . ';' . $this->count;
    }

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->reason)) {
            $this->reason = (string) $xmlElement->reason;
        }

        if (isset($xmlElement->count)) {
            $this->count = (int) (string) $xmlElement->count;
        }
    }

    /**
     * For future use, not implemented yet.
     *
     * Serialization to a simple XML element.
     *
     * @return SimpleXMLElement contains the serialized representation of the object
     *
     * @throws Exception
     */
    public function toXML()
    {
        $xmlString = '<?xml version="1.0"?><unsubscription_reason></unsubscription_reason>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->reason)) {
            $xml->addChild('reason', $this->reason);
        }

        if (isset($this->count)) {
            $xml->addChild('count', $this->count);
        }

        return $xml;
    }
}
