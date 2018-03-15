<?php

namespace XQueue\Maileon\API\XML;

/**
 * Abstract base class for all XML wrapper elements.
 *
 * @author Felix Heinrichs | Trusted Mails GmbH | <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
abstract class AbstractXMLWrapper
{
    /**
     * Initialization from a simple xml element.
     *
     * @param SimpleXMLElement $xmlElement
     *  the SimpleXMLElement to initialize the object from
     */
    abstract function fromXML($xmlElement);

    /**
     * Initialization from a xml serialized string.
     *
     * @param string $xmlString
     *  the raw XML to initialize the object from
     */
    function fromXMLString($xmlString)
    {
        $xmlElement = simplexml_load_string($xmlString);
        $this->fromXML($xmlElement);
    }

    /**
     * Serialization to a simple XML element.
     *
     * @return \em SimpleXMLElement
     *  contains the serialized representation of the object
     */
    abstract function toXML();

    /**
     * Serialization to an XML string.
     *
     * @return \em string
     *  contains the serialized representation of the object
     *
     */
    function toXMLString()
    {
        $result = $this->toXML();
        return $result->asXML();
    }

    /**
     * @return string
     *  a human-readable representation of the object
     */
    abstract function __toString();
}
