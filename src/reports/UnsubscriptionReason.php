<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

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
     * @var integer
     */
    public $count;

    /**
     * @return string
     *  containing a human-readable representation of this unsubscription reason
     */
    public function toString()
    {
        return "Unsubscriber [reason=" . $this->reason .
        ", count=" . $this->count ."]";
    }

    /**
     * @return string containing a csv pepresentation of this unsubscription reason
     */
    public function toCsvString()
    {
        return $this->reason .
        ";" . $this->count;
    }

    /**
     * Initializes this unsubscription reason from an XML representation.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the XML representation to use
     */
    public function fromXML($xmlElement)
    {

        if (isset($xmlElement->reason)) {
            $this->reason = (string)$xmlElement->reason;
        }
        if (isset($xmlElement->count)) {
            $this->count = intval((string)$xmlElement->count);
        }
    }

    /**
     * For future use, not implemented yet.
     *
     * @return \SimpleXMLElement
     *  containing the XML serialization of this object
     */
    public function toXML()
    {
        // Not implemented yet.
    }
}
