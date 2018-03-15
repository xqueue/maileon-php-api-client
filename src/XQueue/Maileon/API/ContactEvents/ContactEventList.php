<?php

namespace XQueue\Maileon\API\ContactEvents;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;
use XQueue\Maileon\API\XML\XMLUtils;

/**
 * The wrapper class for a Maileon contact event list.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class ContactEventList extends AbstractXMLWrapper
{
    public $token;
    public $events;

    /**
     * Creates a new ContactEventList.
     */
    function __construct()
    {
        $this->events = array();
    }

    /**
     * Adds an event to the list.
     *
     * @param com_maileon_api_contactevents_ContactEvent $event
     *    the event to add
     */
    function addEvent($event)
    {
        array_push($this->events, $event);
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
     *    containing the XML serialization of this object
     */
    function toXML()
    {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><events></events>");

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->token)) $xml->addChild("token", $this->token);


        if (count($this->events)) {
            foreach ($this->events as $event) {
                XMLUtils::appendChild($xml, $event->toXML());
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
     *    a human-readable representation of this ContactEventList that shows all contained ContactEvents.
     */
    function toString()
    {

        // Generate standard field string
        $events = "";
        if (array_count_values($this->events) > 0) {
            foreach ($this->events as $event) {
                $events .= $event->toString() . ",";
            }
            $events = rtrim($events, ',');
        }

        return "ContactEventList [token=" . $this->token . ", events={" . $events . "}]";
    }
}