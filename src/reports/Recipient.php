<?php

namespace de\xqueue\maileon\api\client\reports\Reports;

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
     * @return string
     *  containing a human-readable representation of this recipient
     */
    public function toString()
    {
        return "Recipient [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId . "]";
    }

    /**
     * @return string
     *  containing a csv pepresentation of this recipient
     */
    public function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId;
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
    }

    /**
     * For future use, not implemented yet.
     *
     * @return \SimpleXMLElement
     *  containing the XML serialization of this object
     */
    public function toXML()
    {
        // Not implemented yet.
    }
}
