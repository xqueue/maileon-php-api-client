<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

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
     * @var int
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
     * @var int
     */
    public $mailingId;

    /**
     * @var string
     */
    public $mailingName;

    /**
     * @var int
     */
    public $siteId;

    /**
     * @var string
     */
    public $siteName;

    /**
     * @var int
     */
    public $goalId;

    /**
     * @var string
     */
    public $goalName;

    /**
     * @var int
     */
    public $linkId;

    /**
     * @var string
     */
    public $linkUrl;

    public function toString(): string
    {
        return 'Conversion ['
            . 'timestamp=' . $this->timestamp
            . ', timestampSql=' . $this->timestampSql
            . ', contactEmail=' . $this->contactEmail
            . ', contactExternalId=' . $this->contactExternalId
            . ', value=' . $this->value
            . ', mailingSentTimestamp=' . $this->mailingSentTimestamp
            . ', mailingSentTimestampSql=' . $this->mailingSentTimestampSql
            . ', mailingId=' . $this->mailingId
            . ', mailingName=' . $this->mailingName
            . ', siteId=' . $this->siteId
            . ', siteName=' . $this->siteName
            . ', goalId=' . $this->goalId
            . ', goalName=' . $this->goalName
            . ', linkId=' . $this->linkId
            . ', linkUrl=' . $this->linkUrl
            . ']';
    }

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
     * CSV representation of this wrapper.
     *
     * @return string
     */
    public function toCsvString(): string
    {
        return $this->timestamp
            . ';' . $this->timestampSql
            . ';' . $this->contactId
            . ';' . $this->contactEmail
            . ';' . $this->contactExternalId
            . ';' . $this->value
            . ';' . $this->mailingSentTimestamp
            . ';' . $this->mailingSentTimestampSql
            . ';' . $this->mailingId
            . ';' . $this->mailingName
            . ';' . $this->siteId
            . ';' . $this->siteName
            . ';' . $this->goalId
            . ';' . $this->goalName
            . ';' . $this->linkId
            . ';' . $this->linkUrl;
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
        $xmlString = '<?xml version="1.0"?><conversion></conversion>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->timestamp)) {
            $xml->addChild('timestamp', $this->timestamp);
        }

        if (isset($this->timestampSql)) {
            $xml->addChild('timestamp_sql', $this->timestampSql);
        }

        if (isset($this->contactId)) {
            $xml->addChild('contact_id', $this->contactId);
        }

        if (isset($this->contactEmail)) {
            $xml->addChild('contact_email', $this->contactEmail);
        }

        if (isset($this->contactExternalId)) {
            $xml->addChild('contact_external_id', $this->contactExternalId);
        }

        if (isset($this->value)) {
            $xml->addChild('value', $this->value);
        }

        if (isset($this->mailingSentTimestamp)) {
            $xml->addChild('mailing_sent_date', $this->mailingSentTimestamp);
        }

        if (isset($this->mailingSentTimestampSql)) {
            $xml->addChild('mailing_sent_date_sql', $this->mailingSentTimestampSql);
        }

        if (isset($this->mailingId)) {
            $xml->addChild('mailing_id', $this->mailingId);
        }

        if (isset($this->mailingName)) {
            $xml->addChild('mailing_name', $this->mailingName);
        }

        if (isset($this->siteId)) {
            $xml->addChild('site_id', $this->siteId);
        }

        if (isset($this->siteName)) {
            $xml->addChild('site_name', $this->siteName);
        }

        if (isset($this->goalId)) {
            $xml->addChild('goal_id', $this->goalId);
        }

        if (isset($this->goalName)) {
            $xml->addChild('goal_name', $this->goalName);
        }

        if (isset($this->linkId)) {
            $xml->addChild('link_id', $this->linkId);
        }

        if (isset($this->linkUrl)) {
            $xml->addChild('link_url', $this->linkUrl);
        }

        return $xml;
    }
}
