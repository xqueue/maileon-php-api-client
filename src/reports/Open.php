<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

/**
 * This class represents an opener containing the timestamp, the contact, and the ID of the mailing the open.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Open extends AbstractXMLWrapper
{
    /**
     * @var string
     */
    public $timestamp;

    /**
     * @var ReportContact
     */
    public $contact;

    /**
     * @var int
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
     * @var int
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
     * Information about the client of the contact
     *
     * @var ReportClientInfos
     */
    public $clientInfos;

    public function __construct()
    {
        $this->clientInfos = new ReportClientInfos();
    }

    public function toString(): string
    {
        return 'Open ['
            . 'timestamp=' . $this->timestamp
            . ', contact=' . $this->contact->toString()
            . ', mailingId=' . $this->mailingId
            . ', clientInfos=' . $this->clientInfos->toString()
            . ', transactionId=' . $this->transactionId
            . ', contactHash=' . $this->contactHash
            . ', messageId=' . $this->messageId
            . ', format=' . $this->format
            . ', deviceType=' . $this->deviceType
            . ']';
    }

    /**
     * CSV representation of this wrapper.
     *
     * @return string
     */
    public function toCsvString(): string
    {
        return $this->timestamp
            . ';' . $this->contact->toCsvString()
            . ';' . $this->mailingId
            . ';' . $this->clientInfos->toCsvString()
            . ';' . $this->transactionId
            . ';' . $this->contactHash
            . ';' . $this->messageId
            . ';' . $this->format
            . ';' . $this->deviceType;
    }

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
     * Serialization to a simple XML element.
     *
     * @return SimpleXMLElement contains the serialized representation of the object
     *
     * @throws Exception
     */
    public function toXML()
    {
        $xmlString = '<?xml version="1.0"?><open></open>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->mailingId)) {
            $xml->addChild('mailing_id', $this->mailingId);
        }

        if (isset($this->timestamp)) {
            $xml->addChild('timestamp', $this->timestamp);
        }

        if (isset($this->transactionId)) {
            $xml->addChild('transaction_id', $this->transactionId);
        }

        if (isset($this->contactHash)) {
            $xml->addChild('contact_hash', $this->contactHash);
        }

        if (isset($this->messageId)) {
            $xml->addChild('msg_id', $this->messageId);
        }

        if (isset($this->format)) {
            $xml->addChild('format', $this->format);
        }

        if (isset($this->deviceType)) {
            $xml->addChild('device_type', $this->deviceType);
        }

        if (isset($this->clientInfos)) {
            $xml->addChild('client', $this->clientInfos->toXML());
        }

        return $xml;
    }
}
