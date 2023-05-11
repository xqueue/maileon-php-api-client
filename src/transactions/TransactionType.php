<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * Wrapper class for Maileon transaction types.
 *
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
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
     * @var string
     */
    public $description;

    /**
     *
     * @var array
     */
    public $attributes;

    /**
     *
     * @var integer Archiving duration in days [1..n] after which the transaction events will be deleted,
     * 0 and empty = forever (default)
     */
    public $archivingDuration;

    /**
     *
     * @var boolean If true, event has extended limitations for data types
     * (see https://dev.maileon.com/api/rest-api-1-0/transactions/create-transaction-type/) but cannot
     * be used in further logic like contact filters.
     */
    public $storeOnly;

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
     *     Archiving duration in days [1..n] after which the transaction events will be deleted,
     *     0 and empty = forever (default)
     * @param integer $storeOnly
     *     If nothing or false is specified, the limit for strings attributes of the transaction is set
     *     to 1000 characters
     *     and the transaction event can be used in any contactfilter, e.g. "give me all contacts that received contact
     *     event X with value Y in attribute Z".
     *     If storeOnly is set to true, the attributes of the transaction cannot be used as comparision inputs for
     *     contactfilters but the allowed length is raised to 64.000 characters.
     * @param string $description
     *     the description of the transaction type
     */
    public function __construct(
        $id = null,
        $name = null,
        $attributes = array(),
        $archivingDuration = null,
        $storeOnly = false,
        $description = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->attributes = $attributes;
        $this->archivingDuration = $archivingDuration;
        $this->storeOnly = $storeOnly;
        $this->description = $description;
    }

    /**
     * Initializes this transaction type from an XML representation.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the serialized XML representation to use
     */
    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) {
            $this->id = (int)$xmlElement->id;
        }
        if (isset($xmlElement->name)) {
            $this->name = (string)$xmlElement->name;
        }
        if (isset($xmlElement->description)) {
            $this->description = (string)$xmlElement->description;
        }
        if (isset($xmlElement->archivingDuration)) {
            $this->archivingDuration = (int)$xmlElement->archivingDuration;
        }
        if (isset($xmlElement->storeOnly)) {
            $this->storeOnly = ((string)$xmlElement->storeOnly) === 'true';
        }

        if (isset($xmlElement->attributes)) {
            $this->attributes = array();
            foreach ($xmlElement->attributes->children() as $xmlAttribute) {
                $attribute = new AttributeType();
                if (isset($xmlAttribute->id)) {
                    $attribute->id = (int)$xmlAttribute->id;
                }
                if (isset($xmlAttribute->name)) {
                    $attribute->name = trim((string)$xmlAttribute->name);
                }
                if (isset($xmlAttribute->description)) {
                    $attribute->description = trim((string)$xmlAttribute->description);
                }
                if (isset($xmlAttribute->type)) {
                    $attribute->type = DataType::getDataType($xmlAttribute->type);
                }
                if (isset($xmlAttribute->required)) {
                    $attribute->required = ((string)$xmlAttribute->required) === 'true';
                }

                array_push($this->attributes, $attribute);
            }
        }
    }

    /**
     * @return string
     *  a human-readable representation of this object
     */
    public function toString()
    {
        // Generate attributes string
        $attributes = "[";
        if (isset($this->attributes)) {
            foreach ($this->attributes as $index => $value) {
                $attributes .= "attribute (id=" . $value->id . ", name=" . $value->name .
                    ", description=" . ((!empty($value->description))?$value->description:"") .
                    ", type=" . $value->type->getValue() . ", required=" .
                    (($value->required == true) ? "true" : "false") . "), ";
            }
            $attributes = rtrim($attributes, ' ');
            $attributes = rtrim($attributes, ',');
        }
        $attributes .= "]";

        return "TransactionType [id=" . $this->id . ", name=" . $this->name . ", description="
            . $this->description . ", archivingDuration=" . $this->archivingDuration . ", storeOnly="
            . $this->storeOnly . ", attributes=" . $attributes . "]";
    }

    /**
     * @return \SimpleXMLElement
     *  containing the XML serialization of this object
     */
    public function toXML()
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><transaction_type></transaction_type>");

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->id)) {
            $xml->addChild("id", $this->id);
        }
        if (isset($this->name)) {
            $xml->addChild("name", $this->name);
        }
        if (isset($this->description)) {
            $xml->addChild("description", $this->description);
        }
        if (isset($this->archivingDuration)) {
            $xml->addChild("archivingDuration", $this->archivingDuration);
        }
        if (isset($this->storeOnly)) {
            $xml->addChild("storeOnly", ($this->storeOnly === true || $this->storeOnly === "true")?"true":"false");
        }

        if (isset($this->attributes) && sizeof($this->attributes) > 0) {
            $attributes = $xml->addChild("attributes");
            foreach ($this->attributes as $index => $value) {
                $field = $attributes->addChild("attribute");
                if (!empty($value->id)) {
                    $field->addChild("id", $value->id);
                }
                if (!empty($value->name)) {
                    $field->addChild("name", $value->name);
                }
                if (!empty($value->description)) {
                    $field->addChild("description", $value->description);
                }
                if (!empty($value->type)) {
                    $field->addChild("type", $value->type->getValue());
                }
                if (!empty($value->required)) {
                    $field->addChild(
                        "required",
                        ($value->required === true || $value->required === "true") ? "true" : "false"
                    );
                }
            }
        }

        return $xml;
    }

    /**
     * @return string
     *  containing the XML serialization of this object
     */
    public function toXMLString()
    {
        $xml = $this->toXML();
        return $xml->asXML();
    }

    /**
     * @return TransactionType
     *  sanitize this transaction type by removing the ID of ebery single sttribute and return this object for more fluent programming styles
     */
    public function sanitize() {
        if (isset($this->attributes) && sizeof($this->attributes) > 0) {
            /** @var AttributeType $value  */
            foreach ($this->attributes as $index => $value) {
                $value->id = null;
            }
        }
        return $this;
    }
}
