<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use de\xqueue\maileon\api\client\xml\XMLUtils;
use Exception;
use SimpleXMLElement;

use function rtrim;
use function trim;

/**
 * The wrapper class for a Maileon mailing. This class wraps the XML structure.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Mailing extends AbstractXMLWrapper
{
    public $id;
    public $fields;

    /**
     * Constructor initializing default values.
     *
     * @param int   $id     The Maileon mailing id.
     * @param array $fields An array of fields.
     */
    public function __construct(
        $id = null,
        $fields = []
    ) {
        $this->id     = $id;
        $this->fields = $fields;
    }

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) {
            $this->id = $xmlElement->id;
        }

        if (isset($xmlElement->fields)) {
            $this->fields = [];

            foreach ($xmlElement->fields->children() as $field) {
                $this->fields[trim($field->name)] = (string) $field->value;
                // The trim is required to make a safer string from the object
            }
        }
    }

    /**
     * Returns the value of the field with the given name
     *
     * @param string $fieldName The field name of the element to return the value of
     *
     * @return string|null The value or undefined, if not found
     */
    public function getFieldValue($fieldName)
    {
        $name = trim($fieldName);

        if (isset($this->fields)) {
            return $this->fields[$name];
        }

        return null;
    }

    /**
     * Serialization to a simple XML element.
     *
     * @param bool $addXMLDeclaration
     *
     * @return SimpleXMLElement contains the serialized representation of the object
     *
     * @throws Exception
     */
    public function toXML($addXMLDeclaration = true)
    {
        $xmlString = $addXMLDeclaration ? '<?xml version="1.0"?><mailing></mailing>' : '<mailing></mailing>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->id)) {
            $xml->addChild('id', $this->id);
        }

        if (isset($this->fields)) {
            $standard_fields = $xml->addChild('fields');

            foreach ($this->fields as $index => $value) {
                $field = $standard_fields->addChild('field');
                $field->addChild('name', $index);

                XMLUtils::addChildAsCDATA($field, 'value', $value);
            }
        }

        return $xml;
    }

    public function toString(): string
    {
        // Generate standard field string
        $fields = '';

        if (isset($this->fields)) {
            foreach ($this->fields as $index => $value) {
                $fields .= $index . '=' . $value . ',';
            }

            $fields = rtrim($fields, ',');
        }

        return 'Mailing ['
            . 'id=' . $this->id
            . ', fields={' . $fields . '}'
            . ']';
    }
}
