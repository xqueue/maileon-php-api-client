<?php

namespace de\xqueue\maileon\api\client\xml;

use Exception;
use SimpleXMLElement;

use function simplexml_load_string;

/**
 * Abstract base class for all XML wrapper elements.
 *
 * @author Felix Heinrichs
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
abstract class AbstractXMLWrapper
{
    /**
     * Initialization from a xml serialized string.
     *
     * @param string $xmlString the raw XML to initialize the object from
     */
    public function fromXMLString($xmlString)
    {
        $xmlElement = simplexml_load_string($xmlString);

        $this->fromXML($xmlElement);
    }

    /**
     * Initialization from a simple xml element.
     *
     * @param SimpleXMLElement $xmlElement the SimpleXMLElement to initialize the object from
     */
    abstract public function fromXML($xmlElement);

    /**
     * Serialization to an XML string.
     *
     * @return string the serialized representation of the object
     *
     * @throws Exception
     */
    public function toXMLString(): string
    {
        return $this->toXML()->asXML();
    }

    /**
     * Serialization to a simple XML element.
     *
     * @return SimpleXMLElement contains the serialized representation of the object
     *
     * @throws Exception
     */
    abstract public function toXML();

    /**
     * Human-readable representation of the object
     *
     * @return string A human-readable representation of the object
     */
    abstract public function toString(): string;
}
