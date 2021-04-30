<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for a Maileon mailing blacklist (Versandsperrliste in German). This class wraps the XML structure.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class MailingBlacklist extends AbstractXMLWrapper
{
    public $id;
    public $name;
    public $createdTime;
    public $createdUser;

    /**
     * Constructor initializing default values.
     *
     * @param integer $id
     * @param string $name
     * @param integer $createdTime
     * @param string $createdUser
     */
    public function __construct(
        $id = null,
        $name = null,
        $createdTime = null,
        $createdUser = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdTime = $createdTime;
        $this->createdUser = $createdUser;
    }

    /**
     * Initialization of the blacklist from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the attachment from.
     */
    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) {
            $this->id = (int)$xmlElement->id;
        }
        if (isset($xmlElement->name)) {
            $this->name = (string)$xmlElement->name;
        }
        if (isset($xmlElement->created_time)) {
            $this->createdTime = (int)$xmlElement->created_time;
        }
        if (isset($xmlElement->created_user)) {
            $this->createdUser = (string)$xmlElement->created_user;
        }
    }

    /**
     * Creates the XML representation of a mailing blacklist
     *
     * @return \SimpleXMLElement
     */
    public function toXML()
    {
        $xmlString = "<?xml version=\"1.0\"?><mailing_blacklist></mailing_blacklist>";
        $xml = new \SimpleXMLElement($xmlString);
        
        if (isset($this->id)) {
            $xml->addChild("id", $this->id);
        }
        if (isset($this->name)) {
            $xml->addChild("name", $this->name);
        }
        if (isset($this->createdTime)) {
            $xml->addChild("created_time", $this->createdTime);
        }
        if (isset($this->createdUser)) {
            $xml->addChild("created_user", $this->createdUser);
        }
    }
    
    /**
     * Human readable representation of this wrapper.
     *
     * @return string
     *  A human readable version of the mailing.
     */
    public function toString()
    {
        return "MailingBlacklist [id=" . $this->id . ", "
            . "name=" . $this->name . ", "
            . "createdTime=" . $this->createdTime . ", "
            . "createdUser=" . $this->createdUser . "]";
    }
}