<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

use function rtrim;

/**
 * This class represents a click containing the timestamp, the contact, and the ID of the mailing.
 *
 * @author Jannik Jochem
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Click extends AbstractXMLWrapper
{
    /**
     * @var int
     */
    public $timestamp;

    /**
     * @var ReportContact
     */
    public $contact;

    /**
     * @var int
     */
    public $mailingId;

    /**
     * @var int
     */
    public $linkId;

    /**
     * @var string
     */
    public $linkType;

    /**
     * @var string
     */
    public $linkUrl;

    /**
     * @var array
     */
    public $linkTags;

    /**
     * @var string
     */
    public $transactionId;

    /**
     * @var string
     */
    public $contactHash;

    /**
     * @var int
     */
    public $messageId;

    /**
     * @var string
     */
    public $format;

    /**
     * @var string
     */
    public $deviceType;

    /**
     * Information about the client of the contact
     *
     * @var ReportClientInfos
     */
    public $clientInfos;

    public function __construct()
    {
        $this->clientInfos = new ReportClientInfos();
    }

    public function toString(): string
    {
        // Generate custom field string
        $linkTags = '';

        if (isset($this->linkTags)) {
            foreach ($this->linkTags as $value) {
                $linkTags .= $value . '#';
            }

            $linkTags = rtrim($linkTags, '#');
        }

        return 'Click ['
            . 'timestamp=' . $this->timestamp
            . ', contact=' . $this->contact->toString()
            . ', mailingId=' . $this->mailingId
            . ', linkId=' . $this->linkId
            . ', linkType=' . $this->linkType
            . ', linkUrl=' . $this->linkUrl
            . ', linkTags=' . $linkTags
            . ', clientInfos=' . $this->clientInfos->toString()
            . ', transactionId=' . $this->transactionId
            . ', contactHash=' . $this->contactHash
            . ', messageId=' . $this->messageId
            . ', format=' . $this->format
            . ', deviceType=' . $this->deviceType
            . ']';
    }

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

        if (isset($xmlElement->link_id)) {
            $this->linkId = $xmlElement->link_id;
        }

        if (isset($xmlElement->link_type)) {
            $this->linkType = $xmlElement->link_type;
        }

        if (isset($xmlElement->link_url)) {
            $this->linkUrl = $xmlElement->link_url;
        }

        if (isset($xmlElement->transaction_id)) {
            $this->transactionId = $xmlElement->transaction_id;
        }

        if (isset($xmlElement->contact_hash)) {
            $this->contactHash = $xmlElement->contact_hash;
        }

        if (isset($xmlElement->msg_id)) {
            $this->messageId = $xmlElement->msg_id;
        }

        if (isset($xmlElement->format)) {
            $this->format = $xmlElement->format;
        }

        if (isset($xmlElement->device_type)) {
            $this->deviceType = $xmlElement->device_type;
        }

        if (isset($xmlElement->link_tags)) {
            $this->linkTags = [];

            foreach ($xmlElement->link_tags->children() as $field) {
                $this->linkTags[] = $field[0];
            }
        }

        if (isset($xmlElement->client)) {
            $this->clientInfos->fromXML($xmlElement->client);
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
            . ';' . $this->contact->toCsvString()
            . ';' . $this->mailingId
            . ';' . $this->linkId
            . ';' . $this->linkType
            . ';' . $this->linkUrl
            . ';' . $this->clientInfos->toCsvString()
            . ';' . $this->transactionId
            . ';' . $this->contactHash
            . ';' . $this->messageId
            . ';' . $this->format
            . ';' . $this->deviceType;
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
        $xmlString = '<?xml version="1.0"?><click></click>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->contact)) {
            $xml->addChild('contact', $this->contact->toXML());
        }

        if (isset($this->mailingId)) {
            $xml->addChild('mailing_id', $this->mailingId);
        }

        if (isset($this->timestamp)) {
            $xml->addChild('timestamp', $this->timestamp);
        }

        if (isset($this->linkId)) {
            $xml->addChild('link_id', $this->linkId);
        }

        if (isset($this->linkType)) {
            $xml->addChild('link_type', $this->linkType);
        }

        if (isset($this->linkUrl)) {
            $xml->addChild('link_url', $this->linkUrl);
        }

        if (isset($this->transactionId)) {
            $xml->addChild('transaction_id', $this->transactionId);
        }

        if (isset($this->contactHash)) {
            $xml->addChild('contact_hash', $this->contactHash);
        }

        if (isset($this->messageId)) {
            $xml->addChild('msg_id', $this->messageId);
        }

        if (isset($this->format)) {
            $xml->addChild('format', $this->format);
        }

        if (isset($this->deviceType)) {
            $xml->addChild('device_type', $this->deviceType);
        }

        if (isset($this->linkTags)) {
            $linkTags = $xml->addChild('link_tags');

            foreach ($this->linkTags as $linkTag) {
                $linkTags->addChild('field', $linkTag);
            }
        }

        if (isset($this->clientInfos)) {
            $xml->addChild('client', $this->clientInfos->toXML());
        }

        return $xml;
    }
}
