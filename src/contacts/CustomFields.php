<?php

namespace de\xqueue\maileon\api\client\contacts;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use SimpleXMLElement;

use function rtrim;
use function trim;

/**
 * The wrapper class for a list of custom fields.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class CustomFields extends AbstractXMLWrapper
{

    public $custom_fields;

    /**
     * Constructor initializing the list from an array.
     *
     * @param array $custom_fields The list of custom fields. Empty array if no argument passed.
     */
    public function __construct($custom_fields = [])
    {
        $this->custom_fields = $custom_fields;
    }

    public function fromXML($xmlElement)
    {
        foreach ($xmlElement->children() as $field) {
            $this->custom_fields[trim($field->name)] = $field->type;
            // The trim is required to make a safer string from the object
        }
    }

    public function toXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><custom_fields></custom_fields>');

        if (isset($this->custom_fields)) {
            foreach ($this->custom_fields as $index => $type) {
                $field = $xml->addChild('field');
                $field->addChild('name', $index);
                $field->addChild('type', $type);
            }
        }

        return $xml;
    }

    public function toString(): string
    {
        // Generate custom field string
        $custom_fields = '';

        if (isset($this->custom_fields)) {
            foreach ($this->custom_fields as $index => $type) {
                $custom_fields .= $index . '=' . $type . ', ';
            }

            $custom_fields = rtrim($custom_fields, ', ');
        }

        return 'CustomFields = {' . $custom_fields . '}';
    }
}
