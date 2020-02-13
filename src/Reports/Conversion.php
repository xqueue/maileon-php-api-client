<?php

namespace Maileon\Reports;

use Maileon\Xml\AbstractXMLWrapper;

/**
 * This class represents a conversion
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Conversion extends AbstractXMLWrapper
{
    /**
     * @var string
     */
    public $timestamp;

    /**
     * @var string
     */
    public $timestampSql;

    /**
     * @var long
     */
    public $contactId;

    /**
     * @var string
     */
    public $contactEmail;

    /**
     * @var string
     */
    public $contactExternalId;

    /**
     * @var double
     */
    public $value;

    /**
     * @var string
     */
    public $mailingSentTimestamp;

    /**
     * @var string
     */
    public $mailingSentTimestampSql;

    /**
     * @var long
     */
    public $mailingId;

    /**
     * @var string
     */
    public $mailingName;

    /**
     * @var long
     */
    public $siteId;

    /**
     * @var string
     */
    public $siteName;

    /**
     * @var long
     */
    public $goalId;

    /**
     * @var string
     */
    public $goalName;

    /**
     * @var long
     */
    public $linkId;

    /**
     * @var string
     */
    public $linkUrl;

    /**
     * @return \em string
     *  containing a human-readable representation of this conversion
     */
    public function toString()
    {

        return "Conversion [" .
            "timestamp=" . $this->timestamp .
            ", timestampSql=" . $this->timestampSql .
            ", contactEmail=" . $this->contactEmail .
            ", contactExternalId=" . $this->contactExternalId .
            ", value=" . $this->value .
            ", mailingSentTimestamp=" . $this->mailingSentTimestamp .
            ", mailingSentTimestampSql=" . $this->mailingSentTimestampSql .
            ", mailingId=" . $this->mailingId .
            ", mailingName=" . $this->mailingName .
            ", siteId=" . $this->siteId .
            ", siteName=" . $this->siteName .
            ", goalId=" . $this->goalId .
            ", goalName=" . $this->goalName .
            ", linkId=" . $this->linkId .
            ", linkUrl=" . $this->linkUrl ."]";
    }

    /**
     * Initializes this conversion from an XML representation.
     *
     * @param SimpleXMLElement $xmlElement
     *  the XML representation to use
     */
    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->timestamp)) {
            $this->timestamp = $xmlElement->timestamp;
        }
        if (isset($xmlElement->timestamp_sql)) {
            $this->timestampSql = $xmlElement->timestamp_sql;
        }
        if (isset($xmlElement->contact_id)) {
            $this->contactId = $xmlElement->contact_id;
        }
        if (isset($xmlElement->contact_email)) {
            $this->contactEmail = $xmlElement->contact_email;
        }
        if (isset($xmlElement->contact_external_id)) {
            $this->contactExternalId = $xmlElement->contact_external_id;
        }
        if (isset($xmlElement->value)) {
            $this->value = $xmlElement->value;
        }
        if (isset($xmlElement->mailing_sent_date)) {
            $this->mailingSentTimestamp = $xmlElement->mailing_sent_date;
        }
        if (isset($xmlElement->mailing_sent_date_sql)) {
            $this->mailingSentTimestampSql = $xmlElement->mailing_sent_date_sql;
        }
        if (isset($xmlElement->mailing_id)) {
            $this->mailingId = $xmlElement->mailing_id;
        }
        if (isset($xmlElement->mailing_name)) {
            $this->mailingName = $xmlElement->mailing_name;
        }
        if (isset($xmlElement->site_id)) {
            $this->siteId = $xmlElement->site_id;
        }
        if (isset($xmlElement->site_name)) {
            $this->siteName = $xmlElement->site_name;
        }
        if (isset($xmlElement->goal_id)) {
            $this->goalId = $xmlElement->goal_id;
        }
        if (isset($xmlElement->goal_name)) {
            $this->goalName = $xmlElement->goal_name;
        }
        if (isset($xmlElement->link_id)) {
            $this->linkId = $xmlElement->link_id;
        }
        if (isset($xmlElement->link_url)) {
            $this->linkUrl = $xmlElement->link_url;
        }
    }

    /**
     * @return \em csv string
     *  containing a csv pepresentation of this conversion
     */
    public function toCsvString()
    {
        return $this->timestamp .
            ";" . $this->timestampSql .
            ";" . $this->contactId .
            ";" . $this->contactEmail .
            ";" . $this->contactExternalId .
            ";" . $this->value .
            ";" . $this->mailingSentTimestamp .
            ";" . $this->mailingSentTimestampSql .
            ";" . $this->mailingId .
            ";" . $this->mailingName .
            ";" . $this->siteId .
            ";" . $this->siteName .
            ";" . $this->goalId .
            ";" . $this->goalName .
            ";" . $this->linkId .
            ";" . $this->linkUrl;
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
