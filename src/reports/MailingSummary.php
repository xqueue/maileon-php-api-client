<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use SimpleXMLElement;

/**
 * This class represents a mailing summary containing the recipients count, the opens count,
 * the clicks count, and other relevant metrics.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class MailingSummary extends AbstractXMLWrapper
{
    /**
     * @var int
     */
    public $mailingId;

    /**
     * @var int
     */
    public $recipients;

    /**
     * @var int
     */
    public $opens;

    /**
     * @var int
     */
    public $opensUnique;

    /**
     * @var int
     */
    public $clicks;

    /**
     * @var int
     */
    public $clicksUnique;

    /**
     * @var int
     */
    public $bounces;

    /**
     * @var int
     */
    public $unsubscriptions;

    /**
     * @var int
     */
    public $replies;

    /**
     * @var bool
     */
    public $archived;

    public function toString(): string
    {
        return 'MailingSummary ['
            . 'mailing_id=' . $this->mailingId
            . ', recipients=' . $this->recipients
            . ', opens=' . $this->opens
            . ', opens_unique=' . $this->opensUnique
            . ', clicks=' . $this->clicks
            . ', clicks_unique=' . $this->clicksUnique
            . ', bounces=' . $this->bounces
            . ', unsubscriptions=' . $this->unsubscriptions
            . ', replies=' . $this->replies
            . ', archived=' . ($this->archived ? 'true' : 'false')
            . ']';
    }

    /**
     * CSV representation of this wrapper.
     *
     * @return string
     */
    public function toCsvString(): string
    {
        return $this->mailingId
            . ';' . $this->recipients
            . ';' . $this->opens
            . ';' . $this->opensUnique
            . ';' . $this->clicks
            . ';' . $this->clicksUnique
            . ';' . $this->bounces
            . ';' . $this->unsubscriptions
            . ';' . $this->replies
            . ';' . ($this->archived ? 'true' : 'false');
    }

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->mailing_id)) {
            $this->mailingId = (int) $xmlElement->mailing_id;
        }

        if (isset($xmlElement->recipients)) {
            $this->recipients = (int) $xmlElement->recipients;
        }

        if (isset($xmlElement->opens)) {
            $this->opens = (int) $xmlElement->opens;
        }

        if (isset($xmlElement->opens_unique)) {
            $this->opensUnique = (int) $xmlElement->opens_unique;
        }

        if (isset($xmlElement->clicks)) {
            $this->clicks = (int) $xmlElement->clicks;
        }

        if (isset($xmlElement->clicks_unique)) {
            $this->clicksUnique = (int) $xmlElement->clicks_unique;
        }

        if (isset($xmlElement->bounces)) {
            $this->bounces = (int) $xmlElement->bounces;
        }

        if (isset($xmlElement->unsubscriptions)) {
            $this->unsubscriptions = (int) $xmlElement->unsubscriptions;
        }

        if (isset($xmlElement->replies)) {
            $this->replies = (int) $xmlElement->replies;
        }

        if (isset($xmlElement->archived)) {
            $this->archived = ((string) $xmlElement->archived) === 'true';
        }
    }

    /**
     * For future use, not implemented yet.
     *
     * Serialization to a simple XML element.
     *
     * @return SimpleXMLElement contains the serialized representation of the object
     */
    public function toXML()
    {
        $xmlString = '<?xml version="1.0"?><mailing_summary></mailing_summary>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->mailingId)) {
            $xml->addChild('mailing_id', $this->mailingId);
        }

        if (isset($this->recipients)) {
            $xml->addChild('recipients', $this->recipients);
        }

        if (isset($this->opens)) {
            $xml->addChild('opens', $this->opens);
        }

        if (isset($this->opensUnique)) {
            $xml->addChild('opens_unique', $this->opensUnique);
        }

        if (isset($this->clicks)) {
            $xml->addChild('clicks', $this->clicks);
        }

        if (isset($this->clicksUnique)) {
            $xml->addChild('clicks_unique', $this->clicksUnique);
        }

        if (isset($this->bounces)) {
            $xml->addChild('bounces', $this->bounces);
        }

        if (isset($this->unsubscriptions)) {
            $xml->addChild('unsubscriptions', $this->unsubscriptions);
        }

        if (isset($this->replies)) {
            $xml->addChild('replies', $this->replies);
        }

        if (isset($this->archived)) {
            $xml->addChild('archived', ($this->archived ? 'true' : 'false'));
        }

        return $xml;
    }
}
