<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

/**
 * This class represents a recipient containing the timestamp, the contact, and the ID of the mailing.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Recipient extends AbstractXMLWrapper
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
     * @var int
     */
    public $messageId;

    public function toString(): string
    {
        return 'Recipient ['
            . 'timestamp=' . $this->timestamp
            . ', contact=' . $this->contact->toString()
            . ', mailingId=' . $this->mailingId
            . ', transactionId=' . $this->transactionId
            . ', messageId=' . $this->messageId
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
            . ';' . $this->transactionId
            . ';' . $this->messageId;
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

        if (isset($xmlElement->msg_id)) {
            $this->messageId = $xmlElement->msg_id;
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
        $xmlString = '<?xml version="1.0"?><recipient></recipient>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->contact)) {
            $xml->addChild('contact', $this->contact->toXML());
        }

        if (isset($this->mailingId)) {
            $xml->addChild('mailing_id', $this->mailingId);
        }

        if (isset($this->timestamp)) {
            $xml->addChild('timestamp', $this->timestamp);
        }

        if (isset($this->transactionId)) {
            $xml->addChild('transaction_id', $this->transactionId);
        }

        if (isset($this->messageId)) {
            $xml->addChild('msg_id', $this->messageId);
        }

        return $xml;
    }
}
