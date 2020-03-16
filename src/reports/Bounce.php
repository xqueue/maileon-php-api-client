<?php

namespace de\xqueue\maileon\api\client\reports\Reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * This class represents a bounce containing the timestamp, the contact, and the ID of the mailing.
 *
 * @author Jannik Jochem
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Bounce extends AbstractXMLWrapper
{
    /**
     * @var String
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
     * Can be transient or permanent
     * @var String
     */
    public $type;

    /**
     * In the form of X.Y.Z
     * @var String
     */
    public $statusCode;

    /**
     * Can be mta-listener or inbound
     * @var String
     */
    public $source;

    /**
     * @return string
     *  containing a human-readable representation of this bounce
     */
    public function toString()
    {
        return "Bounce [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId .
        ", type=" . $this->type .
        ", statusCode=" . $this->statusCode .
        ", source=" . $this->source . "]";
    }

    /**
     * @return string
     *  containing a csv pepresentation of this bounce
     */
    public function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId .
        ";" . $this->type .
        ";" . $this->statusCode .
        ";" . $this->source;
    }

    /**
     * Initializes this bounce from an XML representation.
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
        if (isset($xmlElement->type)) {
            $this->type = $xmlElement->type;
        }
        if (isset($xmlElement->status_code)) {
            $this->statusCode = $xmlElement->status_code;
        }
        if (isset($xmlElement->source)) {
            $this->source = $xmlElement->source;
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
