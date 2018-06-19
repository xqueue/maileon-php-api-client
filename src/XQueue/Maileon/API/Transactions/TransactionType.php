<?php

namespace XQueue\Maileon\API\Transactions;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;
use XQueue\Maileon\API\Transactions\DataType;

/**
 * Wrapper class for Maileon transaction types.
 *
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 * @author Marcus St&auml;nder | Trusted Technologies GmbH | <a href="mailto:marcus.staender@trusted-technologies.de">marcus.staender@trusted-technologies.de</a>
 */
class TransactionType extends AbstractXMLWrapper
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var array
     */
    public $attributes;

    /**
     *
     * @var integer Archiving duration in days [1..n] after which the transaction events will be deleted, 0 and empty = forever (default)
     */
    public $archivingDuration;

    /**
     * Creates a new transaction type object.
     *
     * @param string $id
     *     the ID of the transaction type
     * @param string $name
     *     the name of the transaction type
     * @param array $attributes
     *     an array of com_maileon_api_transactions_AttributeType attributes associated with the transaction
     * @param integer $archivingDuration
     *     Archiving duration in days [1..n] after which the transaction events will be deleted, 0 and empty = forever (default)
     * @param integer $storeOnly
     *     If nothing or false is specified, the limit for strings attributes of the transaction is set to 1000 characters
     *     and the transaction event can be used in any contactfilter, e.g. �give me all contacts that received contact
     *     event X with value Y in attribute Z�.
     *     If storeOnly is set to true, the attributes of the transaction cannot be used as comparision inputs for
     *     contactfilters but the allowed length is raised to 64.000 characters.
     */
    function __construct(
        $id = null,
        $name = null,
        $attributes = array(),
        $archivingDuration = null,
        $storeOnly = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->attributes = $attributes;
        $this->archivingDuration = $archivingDuration;
        $this->storeOnly = $storeOnly;
    }

    /**
     * Initializes this transaction type from an XML representation.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the serialized XML representation to use
     */
    function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) $this->id = $xmlElement->id;
        if (isset($xmlElement->name)) $this->name = $xmlElement->name;

        if (isset($xmlElement->attributes)) {
            $this->attributes = array();
            foreach ($xmlElement->attributes->children() as $xmlAttribute) {
                $attribute = array();
                if (isset($xmlAttribute->id)) $attribute['id'] = trim($xmlAttribute->id);
                if (isset($xmlAttribute->name)) $attribute['name'] = trim($xmlAttribute->name);
                if (isset($xmlAttribute->type)) $attribute['type'] = DataType::getDataType($xmlAttribute->type);
                if (isset($xmlAttribute->required)) $attribute['required'] = $xmlAttribute->required;
                if (isset($xmlAttribute->archivingDuration)) $attribute['archivingDuration'] = $xmlAttribute->archivingDuration;
                if (isset($xmlAttribute->storeOnly)) $attribute['storeOnly'] = $xmlAttribute->storeOnly;
                array_push($this->attributes, $attribute);
            }
        }
    }

    /**
     * @return \em string
     *  a human-readable representation of this object
     */
    function __toString()
    {
        // Generate attributes string
        $attributes = "[";
        if (isset($this->attributes)) {
            foreach ($this->attributes as $index => $value) {
                $attributes .= "attribute (id=" . $value['id'] . ", name=" . $value['name'] . ", type=" . $value['type']->getValue() . ", required=" . (($value['required'] == true) ? "true" : "false") . "), ";
            }
            $attributes = rtrim($attributes, ' ');
            $attributes = rtrim($attributes, ',');
        }
        $attributes .= "]";

        return "TransactionType [id=" . $this->id . ", name=" . $this->name . ", archivingDuration=" . $this->archivingDuration . ", storeOnly=" . $this->storeOnly . ", attributes=" . $attributes . "]";
    }

    /**
     * @return \em \SimpleXMLElement
     *  containing the XML serialization of this object
     */
    function toXML()
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><transaction_type></transaction_type>");

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->id)) $xml->addChild("id", $this->id);
        if (isset($this->name)) $xml->addChild("name", $this->name);
        if (isset($this->archivingDuration)) $xml->addChild("archivingDuration", $this->archivingDuration);
        if (isset($this->storeOnly)) $xml->addChild("storeOnly", ($this->storeOnly==true)?"true":"false");

        if (isset($this->attributes) && sizeof($this->attributes) > 0) {

            $attributes = $xml->addChild("attributes");
            foreach ($this->attributes as $index => $value) {
                $field = $attributes->addChild("attribute");
                $field->addChild("id", $value->id);
                $field->addChild("name", $value->name);
                $field->addChild("type", $value->type->getValue());
                $field->addChild("required", ($value->required == true) ? "true" : "false");
            }
        }

        return $xml;
    }

    /**
     * @return \em string
     *  containing the XML serialization of this object
     */
    function toXMLString()
    {
        $xml = $this->toXML();
        return $xml->asXML();
    }
}