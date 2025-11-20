<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

/**
 * This class represents a block containing the timestamp, the contact, and some details.
 *
 * @author Jannik Jochem
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Block extends AbstractXMLWrapper
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
    public $oldStatus;

    /**
     * @var int
     */
    public $newStatus;

    /**
     * @var string
     */
    public $reason;

    public function toString(): string
    {
        return 'Block ['
            . 'timestamp=' . $this->timestamp
            . ', contact=' . $this->contact->toString()
            . ', oldStatus=' . $this->oldStatus
            . ', newStatus=' . $this->newStatus
            . ', reason=' . $this->reason
            . ']';
    }

    /**
     * CSV representation of this wrapper.
     *
     * @return string
     */
    public function toCsvString(): string
    {
        return 'block'
            . ';' . $this->timestamp
            . ';' . $this->contact->toCsvString()
            . ';' . $this->oldStatus
            . ';' . $this->newStatus
            . ';' . $this->reason;
    }

    public function fromXML($xmlElement)
    {
        $this->contact = new ReportContact();
        $this->contact->fromXML($xmlElement->contact);

        if (isset($xmlElement->timestamp)) {
            $this->timestamp = $xmlElement->timestamp;
        }

        if (isset($xmlElement->old_status)) {
            $this->oldStatus = $xmlElement->old_status;
        }

        if (isset($xmlElement->new_status)) {
            $this->newStatus = $xmlElement->new_status;
        }

        if (isset($xmlElement->reason)) {
            $this->reason = $xmlElement->reason;
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
        $xmlString = '<?xml version="1.0"?><block></block>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->contact)) {
            $xml->addChild('contact', $this->contact->toXML());
        }

        if (isset($this->timestamp)) {
            $xml->addChild('timestamp', $this->timestamp);
        }

        if (isset($this->oldStatus)) {
            $xml->addChild('old_status', $this->oldStatus);
        }

        if (isset($this->newStatus)) {
            $xml->addChild('new_status', $this->newStatus);
        }

        if (isset($this->reason)) {
            $xml->addChild('reason', $this->reason);
        }

        return $xml;
    }
}
