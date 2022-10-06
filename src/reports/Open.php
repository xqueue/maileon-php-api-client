<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * This class represents an opener containing the timestamp, the contact, and the ID of the mailing the open.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Open extends AbstractXMLWrapper
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
     * @var string
     */
    public $contactHash;
    
    /**
     * @var integer
     */
    public $messageId;

    /**
     * @var string
     */
    public $format;

    /**
     * @var string
     */
    public $deviceType;
    
    /**
     *
     * @var ReportClientInfos Information about the client of the contact
     */
    public $clientInfos;
    
    public function __construct()
    {
        $this->clientInfos = new ReportClientInfos();
    }

    /**
     * @return string
     *  containing a human-readable representation of this open
     */
    public function toString()
    {
        return "Open [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId .
        ", clientInfos=" . $this->clientInfos->toString() .
        ", transactionId=" . $this->transactionId .
        ", contactHash=" . $this->contactHash .
        ", messageId=" . $this->messageId .
        ", format=" . $this->format .
        ", deviceType=" . $this->deviceType . "]";
    }

    /**
     * @return string
     *  containing a csv pepresentation of this open
     */
    public function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId .
        ";" . $this->clientInfos->toCsvString() .
        ";" . $this->transactionId .
        ";" . $this->contactHash .
        ";" . $this->messageId .
        ";" . $this->format .
        ";" . $this->deviceType;
    }

    /**
     * Initializes this open from an XML representation.
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
        if (isset($xmlElement->contact_hash)) {
            $this->contactHash = $xmlElement->contact_hash;
        }
        if (isset($xmlElement->msg_id)) {
            $this->messageId = $xmlElement->msg_id;
        }
        if (isset($xmlElement->format)) {
            $this->format = $xmlElement->format;
        }
        if (isset($xmlElement->device_type)) {
            $this->deviceType = $xmlElement->device_type;
        }
        if (isset($xmlElement->client)) {
            $this->clientInfos->fromXML($xmlElement->client);
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
        $xmlString = "<?xml version=\"1.0\"?><open></open>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->mailingId)) {
            $xml->addChild("mailing_id", $this->mailingId);
        }
        if (isset($this->timestamp)) {
            $xml->addChild("timestamp", $this->timestamp);
        }
        if (isset($this->transactionId)) {
            $xml->addChild("transaction_id", $this->transactionId);
        }
        if (isset($this->contactHash)) {
            $xml->addChild("contact_hash", $this->contactHash);
        }
        if (isset($this->messageId)) {
            $xml->addChild("msg_id", $this->messageId);
        }
        if (isset($this->format)) {
            $xml->addChild("format", $this->format);
        }
        if (isset($this->deviceType)) {
            $xml->addChild("device_type", $this->deviceType);
        }
        if (isset($this->clientInfos)) {
            $xml->addChild("client", $this->clientInfos->toXML());
        }

        return $xml;
    }
}
