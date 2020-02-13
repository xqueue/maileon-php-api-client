<?php

namespace Maileon\Reports;

use Maileon\Reports\ReportContact;
use Maileon\Xml\AbstractXMLWrapper;

/**
 * This class represents a subscriber containing the timestamp, the contact, and the ID of
 * the mailing the subscriber was opted-in by.
 *
 * @author Viktor Balogh (Wiera)
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Subscriber extends AbstractXMLWrapper
{
    /**
     * @var long
     */
    public $timestamp;

    /**
     * @var ReportContact
     */
    public $contact;

    /**
     * @var long
     */
    public $mailingId;

    /**
     * @return \em string
     *  containing a human-readable representation of this subscriber
     */
    public function toString()
    {
        return "Subscriber [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId . "]";
    }

    /**
     * @return \em csv string
     *  containing a csv pepresentation of this subscriber
     */
    public function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId;
    }

    /**
     * Initializes this subscriber from an XML representation.
     *
     * @param SimpleXMLElement $xmlElement
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
    }

    /**
     * For future use, not implemented yet.
     *
     * @return \em SimpleXMLElement
     *  containing the XML serialization of this object
     */
    public function toXML()
    {
        // Not implemented yet.
    }
}
