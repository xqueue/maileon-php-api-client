<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * This class represents a unique bounce containing the timestamp, the contact, and the ID of the mailing.
 *
 * @author Marcus St&auml;nder
 */
class UniqueBounce extends AbstractXMLWrapper
{
    /**
     * @var String
     */
    public $timestamp;

    /**
     * @var ReportContact
     */
    public $contact;

    /**
     * @var integer
     */
    public $mailingId;


    /**
     * @var String
     */
    public $lastType;

    /**
     * @var int
     */
    public $count;

    /**
     * @var int
     */
    public $countHard;

    /**
     * @var int
     */
    public $countSoft;

    /**
     * @return string
     *  containing a human-readable representation of this unique bounce
     */
    public function toString()
    {
        return "UniqueBounce [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId .
        ", count=" . $this->count .
        ", countHard=" . $this->countHard .
        ", countSoft=" . $this->countSoft .
        ", lastType=" . $this->lastType . "]";
    }

    /**
     * @return string
     *  containing a csv pepresentation of this unique bounce
     */
    public function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId .
        ";" . $this->count .
        ";" . $this->countHard .
        ";" . $this->countSoft .
        ";" . $this->lastType;
    }

    /**
     * Initializes this unique bounce from an XML representation.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the XML representation to use
     */
    public function fromXML($xmlElement)
    {
        $this->contact = new ReportContact();
        $this->contact->fromXML($xmlElement->contact);

        if (isset($xmlElement->mailing_id)) {
            $this->mailingId = $xmlElement->mailing_id;
        }
        if (isset($xmlElement->timestamp)) {
            $this->timestamp = $xmlElement->timestamp;
        }
        if (isset($xmlElement->last_type)) {
            $this->lastType = $xmlElement->last_type;
        }
        if (isset($xmlElement->count)) {
            $this->count = $xmlElement->count;
        }
        if (isset($xmlElement->count_hard)) {
            $this->countHard = $xmlElement->count_hard;
        }
        if (isset($xmlElement->count_soft)) {
            $this->countSoft = $xmlElement->count_soft;
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
        $xmlString = "<?xml version=\"1.0\"?><unique_bounce></unique_bounce>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->contact)) {
            $xml->addChild("contact", $this->contact->toXML());
        }        
        if (isset($this->mailingId)) {
            $xml->addChild("mailing_id", $this->mailingId);
        }
        if (isset($this->timestamp)) {
            $xml->addChild("timestamp", $this->timestamp);
        }
        if (isset($this->lastType)) {
            $xml->addChild("last_type", $this->lastType);
        }
        if (isset($this->count)) {
            $xml->addChild("count", $this->count);
        }
        if (isset($this->countHard)) {
            $xml->addChild("count_hard", $this->countHard);
        }
        if (isset($this->countSoft)) {
            $xml->addChild("count_soft", $this->countSoft);
        }

        return $xml;
    }
}
