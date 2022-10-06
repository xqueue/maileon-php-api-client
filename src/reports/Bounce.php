<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * This class represents a bounce containing the timestamp, the contact, and the ID of the mailing.
 *
 * @author Jannik Jochem
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Bounce extends AbstractXMLWrapper
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
     * Can be transient or permanent
     * @var String
     */
    public $type;

    /**
     * In the form of X.Y.Z
     * @var String
     */
    public $statusCode;

    /**
     * Can be mta-listener or inbound
     * @var String
     */
    public $source;

    /**
     * @var integer
     */
    public $messageId;

    /**
     * @return string
     *  containing a human-readable representation of this bounce
     */
    public function toString()
    {
        return "Bounce [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId .
        ", type=" . $this->type .
        ", statusCode=" . $this->statusCode .
        ", source=" . $this->source .
        ", messageId=" . $this->messageId . "]";
    }

    /**
     * @return string
     *  containing a csv pepresentation of this bounce
     */
    public function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId .
        ";" . $this->type .
        ";" . $this->statusCode .
        ";" . $this->source .
        ";" . $this->messageId;
    }

    /**
     * Initializes this bounce from an XML representation.
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
        if (isset($xmlElement->type)) {
            $this->type = $xmlElement->type;
        }
        if (isset($xmlElement->status_code)) {
            $this->statusCode = $xmlElement->status_code;
        }
        if (isset($xmlElement->source)) {
            $this->source = $xmlElement->source;
        }
        if (isset($xmlElement->msg_id)) {
            $this->messageId = $xmlElement->msg_id ;
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
        $xmlString = "<?xml version=\"1.0\"?><bounce></bounce>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->contact)) {
            $xml->addChild("contact", $this->contact->toXML());
        }        
        if (isset($this->mailing_id)) {
            $xml->addChild("mailing_id", $this->mailingId);
        }
        if (isset($this->timestamp)) {
            $xml->addChild("timestamp", $this->timestamp);
        }
        if (isset($this->type)) {
            $xml->addChild("type", $this->type);
        }
        if (isset($this->status_code)) {
            $xml->addChild("status_code", $this->statusCode);
        }
        if (isset($this->source)) {
            $xml->addChild("source", $this->source);
        }
        if (isset($this->msg_id)) {
            $xml->addChild("msg_id", $this->messageId);
        }

        return $xml;
    }
}
