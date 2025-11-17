<?php

namespace de\xqueue\maileon\api\client\account;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use SimpleXMLElement;

/**
 * The wrapper class for a Maileon account mailing domain. This class wraps the XML structure.
 *
 * @author Andreas Lange
 */
class MailingDomain extends AbstractXMLWrapper
{
    public $name;
    public $status;
    public $createdTime;
    public $httpReachable;
    public $httpsReachable;
    public $certificateExpires;

    /**
     * Constructor initializing default values.
     *
     * @param $name
     * @param $status
     * @param $createdTime
     * @param $httpReachable
     * @param $httpsReachable
     * @param $certificateExpires
     */
    public function __construct(
        $name = null,
        $status = null,
        $createdTime = null,
        $httpReachable = null,
        $httpsReachable = null,
        $certificateExpires = null
    ) {
        $this->name               = $name;
        $this->status             = $status;
        $this->createdTime        = $createdTime;
        $this->httpReachable      = $httpReachable;
        $this->httpsReachable     = $httpsReachable;
        $this->certificateExpires = $certificateExpires;
    }

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->name)) {
            $this->name = (string) $xmlElement->name;
        }

        if (isset($xmlElement->status)) {
            $this->status = (string) $xmlElement->status;
        }

        if (isset($xmlElement->created_time)) {
            $this->createdTime = (string) $xmlElement->created_time;
        }

        if (isset($xmlElement->http_reachable)) {
            $this->httpReachable = (bool) (string) $xmlElement->http_reachable;
        }

        if (isset($xmlElement->https_reachable)) {
            $this->httpsReachable = (bool) (string) $xmlElement->https_reachable;
        }

        if (isset($xmlElement->certificate_expires)) {
            $this->certificateExpires = (string) $xmlElement->certificate_expires;
        }
    }

    public function toXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><mailing_domain></mailing_domain>');

        $xml->addChild('name', $this->name);
        $xml->addChild('status', $this->status);
        $xml->addChild('created_time', $this->createdTime);
        $xml->addChild('http_reachable', $this->httpReachable ? 'true' : 'false');
        $xml->addChild('https_reachable', $this->httpsReachable ? 'true' : 'false');
        $xml->addChild('certificate_expires', $this->certificateExpires);

        // Add value as CDATA as it can contain special characters
        // $xml->value = null;
        // $node = dom_import_simplexml($xml->value);
        // $no   = $node->ownerDocument;
        // $node->appendChild($no->createCDATASection($this->value));

        return $xml;
    }

    public function toString(): string
    {
        return 'MailingDomain ['
            . 'name=' . $this->name
            . ', status=' . $this->status
            . ', created_time=' . $this->createdTime
            . ', http_reachable=' . ($this->httpReachable ? 'true' : 'false')
            . ', https_reachable=' . ($this->httpsReachable ? 'true' : 'false')
            . ', certificate_expires=' . $this->certificateExpires
            . ']';
    }
}
