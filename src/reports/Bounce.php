<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

/**
 * This class represents a bounce containing the timestamp, the contact, and the ID of the mailing.
 *
 * @author Jannik Jochem
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Bounce extends AbstractXMLWrapper
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
     * Can be transient or permanent
     *
     * @var string
     */
    public $type;

    /**
     * In the form of X.Y.Z
     *
     * @var string
     */
    public $statusCode;

    /**
     * Can be mta-listener or inbound
     *
     * @var string
     */
    public $source;

    /**
     * @var int
     */
    public $messageId;

    public function toString(): string
    {
        return 'Bounce ['
            . 'timestamp=' . $this->timestamp
            . ', contact=' . $this->contact->toString()
            . ', mailingId=' . $this->mailingId
            . ', type=' . $this->type
            . ', statusCode=' . $this->statusCode
            . ', source=' . $this->source
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
            . ';' . $this->type
            . ';' . $this->statusCode
            . ';' . $this->source
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
        $xmlString = '<?xml version="1.0"?><bounce></bounce>';
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

        if (isset($this->type)) {
            $xml->addChild('type', $this->type);
        }

        if (isset($this->statusCode)) {
            $xml->addChild('status_code', $this->statusCode);
        }

        if (isset($this->source)) {
            $xml->addChild('source', $this->source);
        }

        if (isset($this->messageId)) {
            $xml->addChild('msg_id', $this->messageId);
        }

        return $xml;
    }
}
