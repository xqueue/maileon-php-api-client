<?php

namespace de\xqueue\maileon\api\client\reports\Reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * This class represents an unsubscription containing the timestamp, the contact,
 * the ID of the mailing the unsubscription came from, and the source.
 *
 * @author Viktor Balogh (Wiera)
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Unsubscriber extends AbstractXMLWrapper
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
    public $source;

    /**
     * @return string
     *  containing a human-readable representation of this unsubscription
     */
    public function toString()
    {
        return "Unsubscriber [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId .
        ", source=" . $this->source . "]";
    }

    /**
     * @return string containing a csv pepresentation of this unsubscriber
     */
    public function toCsvString()
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
