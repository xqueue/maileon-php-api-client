<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

use function filter_var;

/**
 * This class represents the client infos from a click or open in the reporting.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class ReportClientInfos extends AbstractXMLWrapper
{
    /**
     * Name of the operating system, e.g. WINDOWS_10
     *
     * @var string
     */
    public $os;

    /**
     * Group of the operating system, e.g. WINDOWS, LINUX, ...
     *
     * @var string
     */
    public $osGroup;

    /**
     * Browser name, e.g. CHROME
     *
     * @var string
     */
    public $browser;

    /**
     * Browser group, e.g. CHROME
     *
     * @var string
     */
    public $browserGroup;

    /**
     * Type of the browser, e.g. WebBrowser
     *
     * @var string
     */
    public $browserType;

    /**
     * User-Agent
     *
     * @var string
     */
    public $userAgent;

    /**
     * Name of the rendering engine of the browser
     *
     * @var string
     */
    public $renderingEngine;

    public function fromXML($xmlElement)
    {
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
     * CSV representation of this wrapper.
     *
     * @return string
     */
    public function toCsvString(): string
    {
        return $this->os
            . ';' . $this->osGroup
            . ';' . $this->browser
            . ';' . $this->browserGroup
            . ';' . $this->browserType
            . ';' . $this->userAgent
            . ';' . $this->renderingEngine;
    }

    public function toString(): string
    {
        return 'ClientInfos ['
            . 'os=' . $this->os
            . ', osGroup=' . $this->osGroup
            . ', browser=' . $this->browser
            . ', browserGroup=' . $this->browserGroup
            . ', browserType=' . $this->browserType
            . ', userAgent=' . $this->userAgent
            . ', renderingEngine=' . $this->renderingEngine
            . ']';
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
        $xmlString = '<?xml version="1.0"?><client></client>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->os)) {
            $xml->addChild('os_name', $this->os);
        }

        if (isset($this->osGroup)) {
            $xml->addChild('os_group', $this->osGroup);
        }

        if (isset($this->browser)) {
            $xml->addChild('browser', $this->browser);
        }

        if (isset($this->browserGroup)) {
            $xml->addChild('browser_group', $this->browserGroup);
        }

        if (isset($this->browserType)) {
            $xml->addChild('browser_type', $this->browserType);
        }

        if (isset($this->userAgent)) {
            $xml->addChild('user_agent', $this->userAgent);
        }

        if (isset($this->renderingEngine)) {
            $xml->addChild('rendering_engine', $this->renderingEngine);
        }

        return $xml;
    }
}
