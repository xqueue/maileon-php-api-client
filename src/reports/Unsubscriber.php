<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

/**
 * This class represents an unsubscription containing the timestamp, the contact,
 * the ID of the mailing the unsubscription came from, and the source.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Unsubscriber extends AbstractXMLWrapper
{
    /**
     * @var int
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

    /**
     * @var string
     */
    public $source;

    public function toString(): string
    {
        return 'Unsubscriber ['
            . 'timestamp=' . $this->timestamp
            . ', contact=' . $this->contact->toString()
            . ', mailingId=' . $this->mailingId
            . ', source=' . $this->source
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
            . ';' . $this->source
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

        if (isset($xmlElement->source)) {
            $this->source = $xmlElement->source;
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
        $xmlString = '<?xml version="1.0"?><unsubscriber></unsubscriber>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->contact)) {
            $xml->addChild('contact', $this->contact->toXML());
        }

        if (isset($this->timestamp)) {
            $xml->addChild('timestamp', $this->timestamp);
        }

        if (isset($this->mailingId)) {
            $xml->addChild('mailing_id', $this->mailingId);
        }

        if (isset($this->transactionId)) {
            $xml->addChild('transaction_id', $this->transactionId);
        }

        if (isset($this->messageId)) {
            $xml->addChild('msg_id', $this->messageId);
        }

        if (isset($this->source)) {
            $xml->addChild('source', $this->source);
        }

        return $xml;
    }
}
