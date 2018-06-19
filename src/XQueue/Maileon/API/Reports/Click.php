<?php

namespace XQueue\Maileon\API\Reports;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;
use XQueue\Maileon\API\Reports\ReportContact;

/**
 * This class represents a click containing the timestamp, the contact, and the ID of the mailing.
 *
 * @author Jannik Jochem
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Click extends AbstractXMLWrapper
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
     * @var long
     */
    public $linkId;

    /**
     * @var string
     */
    public $linkUrl;

    /**
     * @var string
     */
    public $linkTags;

    /**
     * @return \em string
     *  containing a human-readable representation of this click
     */
    function toString()
    {

        // Generate custom field string
        $linkTags = "";
        if (isset($this->linkTags)) {
            foreach ($this->linkTags as $value) {
                $linkTags .= $value . "#";
            }
            $linkTags = rtrim($linkTags, '#');
        }

        return "Click [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId .
        ", linkId=" . $this->linkId .
        ", linkUrl=" . $this->linkUrl .
        ", linkTags=" . $linkTags ."]";
    }

    /**
     * Initializes this click from an XML representation.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the XML representation to use
     */
    function fromXML($xmlElement)
    {
        $this->contact = new ReportContact();
        $this->contact->fromXML($xmlElement->contact);

        if (isset($xmlElement->mailing_id)) $this->mailingId = $xmlElement->mailing_id;
        if (isset($xmlElement->timestamp)) $this->timestamp = $xmlElement->timestamp;
        if (isset($xmlElement->link_id)) $this->linkId = $xmlElement->link_id;
        if (isset($xmlElement->link_url)) $this->linkUrl = $xmlElement->link_url;

        if (isset($xmlElement->link_tags)) {
            $this->linkTags = array();
            foreach ($xmlElement->link_tags->children() as $field) {
                array_push($this->linkTags, $field[0]);
            }
        }
    }

    /**
     * @return \em csv string
     *  containing a csv pepresentation of this click
     */
    function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId .
        ";" . $this->linkId .
        ";" . $this->linkUrl;
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
