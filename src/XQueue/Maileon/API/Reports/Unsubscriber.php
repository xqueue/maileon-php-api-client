<?php

namespace XQueue\Maileon\API\Reports;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;
use XQueue\Maileon\API\Reports\ReportContact;

/**
 * This class represents an unsubscription containing the timestamp, the contact, the ID of the mailing the unsubscription came from, and the source.
 *
 * @author Viktor Balogh (Wiera)
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Unsubscriber extends AbstractXMLWrapper
{
    /**
     * @var long
     */
    public $timestamp;

    /**
     * @var com_maileon_api_reports_ReportContact
     */
    public $contact;

    /**
     * @var long
     */
    public $mailingId;

    /**
     * @var string
     */
    public $source;

    /**
     * @return \em string
     *  containing a human-readable representation of this unsubscription
     */
    function toString()
    {
        return "Unsubscriber [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId .
        ", source=" . $this->source . "]";
    }

    /**
     * @return \em csv string
     *  containing a csv pepresentation of this unsubscriber
     */
    function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId .
        ";" . $this->source;
    }

    /**
     * Initializes this unsubscription from an XML representation.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the XML representation to use
     */
    function fromXML($xmlElement)
    {
        $this->contact = new ReportContact();
        $this->contact->fromXML($xmlElement->contact);

        if (isset($xmlElement->mailing_id)) $this->mailingId = $xmlElement->mailing_id;
        if (isset($xmlElement->source)) $this->source = $xmlElement->source;
        if (isset($xmlElement->timestamp)) $this->timestamp = $xmlElement->timestamp;
    }

    /**
     * For future use, not implemented yet.
     *
     * @return \em \SimpleXMLElement
     *  containing the XML serialization of this object
     */
    function toXML()
    {
        // Not implemented yet.
    }
}
