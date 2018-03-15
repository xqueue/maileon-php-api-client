<?php

namespace XQueue\Maileon\API\ContactEvents;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;

/**
 * The wrapper class for a Maileon contact event.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class ContactEvent extends AbstractXMLWrapper
{
    public $email;
    public $external_id;
    public $properties;

    /**
     * Constructor initializing default values.
     */
    function __construct()
    {
        $this->properties = array();
    }

    /**
     * Sets a <code>string</code>, <code>int</code>, <code>float</code> or <code>double</code> value or creates it
     * if it does not exist yes. <code>true</code> will be translated to <code>1</code> and <code>false</code> to
     * <code>0</code>.
     *
     * @param string $key
     *    the name of the attribute to set
     * @param string $value
     *    the new attribute value
     */
    function setProperty($key, $value)
    {
        if ($value === true) $this->properties[$key] = 1;
        else if ($value === false) $this->properties[$key] = 0;
        else $this->properties[$key] = $value;
    }

    /**
     * For future use, not yet implemented.
     *
     * @param SimpleXMLElement $xmlElement
     */
    function fromXML($xmlElement)
    {
    }

    /**
     * @return \em SimpleXMLElement
     *  containing the XML serialization of this object
     */
    function toXML()
    {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><event></event>");

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->email)) $xml->addChild("email", $this->email);
        if (isset($this->external_id)) $xml->addChild("external_id", $this->external_id);

        if (count($this->properties) > 0) {
            foreach ($this->properties as $index => $value) {

                // If value exists, add it, else add nul="true"
                if (isset($value)) {
                    $property = $xml->addChild("property", $value);
                } else {
                    $property = $xml->addChild("property");
                    $property->addAttribute("nil", true);
                }

                $property->addAttribute("key", $index);
            }
        }

        return $xml;
    }

    /**
     * @return \em string
     *    containing the XML serialization of this object
     */
    function toXMLString()
    {
        $xml = $this->toXML();
        return $xml->asXML();
    }

    /**
     * @return \em string
     *    a human-readable representation listing all the attributes of this event and their respective values.
     */
    function toString()
    {

        // Generate standard field string
        $properties = "";
        if (count($this->properties) > 0) {
            foreach ($this->properties as $index => $value) {
                $properties .= $index . "=" . $value . ",";
            }
            $properties = rtrim($properties, ',');
        }

        return "ContactEvent [email=" . $this->email . ", external_id=" . $this->external_id . ", properties={" . $properties . "}]";
    }
}