<?php

namespace de\xqueue\maileon\api\client\xml;

/**
 * Abstract base class for all XML wrapper elements.
 *
 * @author Felix Heinrichs | Trusted Mails GmbH |
 * <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
abstract class AbstractXMLWrapper
{
    /**
     * Initialization from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the SimpleXMLElement to initialize the object from
     */
    abstract public function fromXML($xmlElement);

    /**
     * Initialization from a xml serialized string.
     *
     * @param string $xmlString
     *  the raw XML to initialize the object from
     */
    public function fromXMLString($xmlString)
    {
        $xmlElement = simplexml_load_string($xmlString);
        $this->fromXML($xmlElement);
    }

    /**
     * Serialization to a simple XML element.
     *
     * @return \SimpleXMLElement
     *  contains the serialized representation of the object
     */
    abstract public function toXML();

    /**
     * Serialization to an XML string.
     *
     * @return string
     *  contains the serialized representation of the object
     *
     */
    public function toXMLString()
    {
        $result = $this->toXML();
        return $result->asXML();
    }

    /**
     * @return string
     *  a human-readable representation of the object
     */
    abstract public function toString();
}
