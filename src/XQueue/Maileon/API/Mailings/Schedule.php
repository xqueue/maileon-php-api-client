<?php

namespace XQueue\Maileon\API\Mailings;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;

/**
 * The wrapper class for a Maileon schedule. This class wraps the XML structure.
 *
 * @author Marcus St&auml;nder
 */
class Schedule extends AbstractXMLWrapper {
    public $minutes;
    public $hours;
    public $state;
    public $date;

    /**
     * Constructor initializing default values.
     *
     * @param number $id
     *  The Maileon mailing id.
     * @param array $fields
     *  An array of fields.
     */
    function __construct(
            $minutes = null,
            $hours = null,
            $state = null,
            $date = null) {
        $this->minutes = $minutes;
        $this->hours = $hours;
        $this->state = $state;
        $this->date = $date;
    }

    /**
     * Initialization of the schedule from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the schedule from.
     */
    function fromXML($xmlElement) {
        if (isset($xmlElement->minutes)) $this->minutes = $xmlElement->minutes;
        if (isset($xmlElement->hours)) $this->hours = $xmlElement->hours;
        if (isset($xmlElement->state)) $this->state = $xmlElement->state;
        if (isset($xmlElement->date)) $this->date = $xmlElement->date;
    }

    /**
     * Serialization to a simple XML element.
     *
     * @param bool $addXMLDeclaration
     *
     * @return \em \SimpleXMLElement
     *  Generate a XML element from the contact object.
     */
    function toXML($addXMLDeclaration = true) {
        $xmlString = $addXMLDeclaration ? "<?xml version=\"1.0\"?><mailing></mailing>" : "<mailing></mailing>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->minutes)) $xml->addChild("minutes", $this->minutes);
        if (isset($this->hours)) $xml->addChild("hours", $this->hour);
        if (isset($this->state)) $xml->addChild("state", $this->state);
        if (isset($this->date)) $xml->addChild("date", $this->date);

        return $xml;
    }

    /**
     * Serialization to a simple XML element as string
     *
     * @return \em string
     *  The string representation of the XML document for this mailing.
     */
    function toXMLString() {
        $xml = $this->toXML();
        return $xml->asXML();
    }

    /**
     * Human readable representation of this wrapper.
     *
     * @return \em string
     *  A human readable version of the schedule.
     */
    function toString() {
        return "Schedule [minutes=" . $this->minutes . ", hours=" . $this->hours . ", state=" . $this->state . ", date=" . $this->date . "]";
    }

    /**
     * Date and time representation of this wrapper.
     *
     * @return \em string
     *  A date time version of the schedule.
     */
    function toDateTime() {
        return $this->date . " " . str_pad($this->hours, 2, '0', STR_PAD_LEFT) . ":" . str_pad($this->minutes, 2, '0', STR_PAD_LEFT);
    }
}