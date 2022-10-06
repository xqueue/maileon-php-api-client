<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * This class represents the client infos from a click or open in the reporting.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class ReportClientInfos extends AbstractXMLWrapper
{
    /**
     * @var String Name of the operating system, e.g. WINDOWS_10
     */
    public $os;
    
    /**
     * @var String Group of the operating system, e.g. WINDOWS, LINUX, ...
     */
    public $osGroup;
    
    /**
     * @var String Browser name, e.g. CHROME
     */
    public $browser;
    
    /**
     * @var String Browser group, e.g. CHROME
     */
    public $browserGroup;
    
    /**
     * @var String Type of the browser, e.g. WebBrowser
     */
    public $browserType;
    
    /**
     * @var String User-Agent
     */
    public $userAgent;
    
    /**
     * @var String Name of the rendering engine of the browser
     */
    public $renderingEngine;
    

    /**
     * Constructor for initializing a Report Contact from a given contact
     * @param Contact $ctontact
     */
    public function __construct() {

    }

    /**
     * Initialization of the client infos from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the contact list from.
     */
    public function fromXML($xmlElement) {
        
        if ($xmlElement) {

            if (isset($xmlElement->os_name)) {
                $this->os = filter_var($xmlElement->os_name, FILTER_SANITIZE_STRING);
            }
            if (isset($xmlElement->os_group)) {
                $this->osGroup = filter_var($xmlElement->os_group, FILTER_SANITIZE_STRING);
            }
            if (isset($xmlElement->browser)) {
                $this->browser = filter_var($xmlElement->browser, FILTER_SANITIZE_STRING);
            }
            if (isset($xmlElement->browser_group)) {
                $this->browserGroup = filter_var($xmlElement->browser_group, FILTER_SANITIZE_STRING);
            }
            if (isset($xmlElement->browser_type)) {
                $this->browserType = filter_var($xmlElement->browser_type, FILTER_SANITIZE_STRING);
            }
            if (isset($xmlElement->user_agent)) {
                $this->userAgent = filter_var($xmlElement->user_agent, FILTER_SANITIZE_STRING);
            }
            if (isset($xmlElement->rendering_engine)) {
                $this->renderingEngine = filter_var($xmlElement->rendering_engine, FILTER_SANITIZE_STRING);
            }
        }
    }

    
    /**
     * @return string
     *  containing a csv pepresentation of the client infos
     */
    public function toCsvString() {
        return $this->os .
        ";" . $this->osGroup .
        ";" . $this->browser .
        ";" . $this->browserGroup .
        ";" . $this->browserType .
        ";" . $this->userAgent .
        ";" . $this->renderingEngine;
    }
    
    /**
     * @return string
     *  containing a human-readable representation of this client info
     */
    public function toString()
    {
        return "ClientInfos [os=" . $this->os .
        ", osGroup=" . $this->osGroup .
        ", browser=" . $this->browser .
        ", browserGroup=" . $this->browserGroup .
        ", browserType=" . $this->browserType .
        ", userAgent=" . $this->userAgent .
        ", renderingEngine=" . $this->renderingEngine . "]";
    }
    
    /**
     * For future use, not implemented yet.
     *
     * @return \SimpleXMLElement
     *  containing the XML serialization of this object
     */
    public function toXML() {
        $xmlString = "<?xml version=\"1.0\"?><client></client>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->os)) {
            $xml->addChild("os_name", $this->os);
        }
        if (isset($this->osGroup)) {
            $xml->addChild("os_group", $this->osGroup);
        }
        if (isset($this->browser)) {
            $xml->addChild("browser", $this->browser);
        }
        if (isset($this->browserGroup)) {
            $xml->addChild("browser_group", $this->browserGroup);
        }
        if (isset($this->browserType)) {
            $xml->addChild("browser_type", $this->browserType);
        }
        if (isset($this->userAgent)) {
            $xml->addChild("user_agent", $this->userAgent);
        }
        if (isset($this->renderingEngine)) {
            $xml->addChild("rendering_engine", $this->renderingEngine);
        }

        return $xml;
    }
}
