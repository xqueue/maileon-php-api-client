<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * This class represents a recipient containing the timestamp, the contact, and the ID of the mailing.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Recipient extends AbstractXMLWrapper
{
    /**
     * @var integer
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
     * @var string
     */
    public $transactionId;
    
    /**
     * @var integer
     */
    public $messageId;

    /**
     * @return string
     *  containing a human-readable representation of this recipient
     */
    public function toString()
    {
        return "Recipient [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId .
        ", transactionId=" . $this->transactionId .
        ", messageId=" . $this->messageId ."]";
    }

    /**
     * @return string
     *  containing a csv pepresentation of this recipient
     */
    public function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId .
        ";" . $this->transactionId .
        ";" . $this->messageId;
    }

    /**
     * Initializes this recipient from an XML representation.
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
        if (isset($xmlElement->transaction_id)) {
            $this->transactionId = $xmlElement->transaction_id;
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
        $xmlString = "<?xml version=\"1.0\"?><recipient></recipient>";
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
        if (isset($this->transactionId)) {
            $xml->addChild("transaction_id", $this->transactionId);
        }

        return $xml;
    }
}
