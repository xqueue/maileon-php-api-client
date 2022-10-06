<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

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
     * @var integer
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
     * @var integer
     */
    public $mailingId;

    /**
     * @var string
     */
    public $mailingName;

    /**
     * @var integer
     */
    public $siteId;

    /**
     * @var string
     */
    public $siteName;

    /**
     * @var integer
     */
    public $goalId;

    /**
     * @var string
     */
    public $goalName;

    /**
     * @var integer
     */
    public $linkId;

    /**
     * @var string
     */
    public $linkUrl;

    /**
     * @return string
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
     * @param \SimpleXMLElement $xmlElement
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
     * @return string
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
     * @return \SimpleXMLElement
     *  containing the XML serialization of this object
     */
    public function toXML()
    {
        $xmlString = "<?xml version=\"1.0\"?><conversion></conversion>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->timestamp)) {
            $xml->addChild("timestamp", $this->timestamp);
        }
        if (isset($this->timestamp_sql)) {
            $xml->addChild("timestamp_sql", $this->timestampSql);
        }
        if (isset($this->contact_id)) {
            $xml->addChild("contact_id", $this->contactId);
        }
        if (isset($this->contact_email)) {
            $xml->addChild("contact_email", $this->contactEmail);
        }
        if (isset($this->contact_external_id)) {
            $xml->addChild("contact_external_id", $this->contactExternalId);
        }
        if (isset($this->value)) {
            $xml->addChild("value", $this->value);
        }
        if (isset($this->mailing_sent_date)) {
            $xml->addChild("mailing_sent_date", $this->mailingSentTimestamp);
        }
        if (isset($this->mailing_sent_date_sql)) {
            $xml->addChild("mailing_sent_date_sql", $this->mailingSentTimestampSql);
        }
        if (isset($this->mailing_id)) {
            $xml->addChild("mailing_id", $this->mailingId);
        }
        if (isset($this->mailing_name)) {
            $xml->addChild("mailing_name", $this->mailingName);
        }
        if (isset($this->site_id)) {
            $xml->addChild("site_id", $this->siteId);
        }
        if (isset($this->site_name)) {
            $xml->addChild("site_name", $this->siteName);
        }
        if (isset($this->goal_id)) {
            $xml->addChild("goal_id", $this->goalId);
        }
        if (isset($this->goal_name)) {
            $xml->addChild("goal_name", $this->goalName);
        }
        if (isset($this->link_id)) {
            $xml->addChild("link_id", $this->linkId);
        }
        if (isset($this->link_url)) {
            $xml->addChild("link_url", $this->linkUrl);
        }

        return $xml;
    }
}
