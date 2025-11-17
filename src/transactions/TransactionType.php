<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use SimpleXMLElement;

use function is_array;
use function rtrim;
use function trim;

/**
 * Wrapper class for Maileon transaction types.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class TransactionType extends AbstractXMLWrapper
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var array
     */
    public $attributes;

    /**
     * Archiving duration in days [1...n] after which the transaction events will be deleted, 0 and empty = forever (default)
     *
     * @var int
     */
    public $archivingDuration;

    /**
     * If true, event has extended limitations for data types
     * (see https://support.maileon.com/support/create-transaction-type/) but cannot
     * be used in further logic like contact filters.
     *
     * @var boolean
     */
    public $storeOnly;

    /**
     * Creates a new transaction type object.
     *
     * @param string $id                the ID of the transaction type
     * @param string $name              the name of the transaction type
     * @param array  $attributes        an array of \de\xqueue\maileon\api\client\transactions\AttributeType attributes associated with the
     *                                  transaction
     * @param int    $archivingDuration Archiving duration in days [1...n] after which the transaction events will be deleted,
     *                                  0 and empty = forever (default)
     * @param int    $storeOnly         If nothing or false is specified, the limit for strings attributes of the transaction is set to
     *                                  1000 characters and the transaction event can be used in any contact filter, e.g. "give me all
     *                                  contacts that received contact event X with value Y in attribute Z". If storeOnly is set to true,
     *                                  the attributes of the transaction cannot be used as comparison inputs for contact filters but the
     *                                  allowed length is raised to 64.000 characters.
     * @param string $description       the description of the transaction type
     */
    public function __construct(
        $id = null,
        $name = null,
        $attributes = [],
        $archivingDuration = null,
        $storeOnly = false,
        $description = null
    ) {
        $this->id                = $id;
        $this->name              = $name;
        $this->attributes        = $attributes;
        $this->archivingDuration = $archivingDuration;
        $this->storeOnly         = $storeOnly;
        $this->description       = $description;
    }

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) {
            $this->id = (int) $xmlElement->id;
        }

        if (isset($xmlElement->name)) {
            $this->name = (string) $xmlElement->name;
        }

        if (isset($xmlElement->description)) {
            $this->description = (string) $xmlElement->description;
        }

        if (isset($xmlElement->archivingDuration)) {
            $this->archivingDuration = (int) $xmlElement->archivingDuration;
        }

        if (isset($xmlElement->storeOnly)) {
            $this->storeOnly = ((string) $xmlElement->storeOnly) === 'true';
        }

        if (isset($xmlElement->attributes)) {
            $this->attributes = [];

            foreach ($xmlElement->attributes->children() as $xmlAttribute) {
                $attribute = new AttributeType();

                if (isset($xmlAttribute->id)) {
                    $attribute->id = (int) $xmlAttribute->id;
                }

                if (isset($xmlAttribute->name)) {
                    $attribute->name = trim((string) $xmlAttribute->name);
                }

                if (isset($xmlAttribute->description)) {
                    $attribute->description = trim((string) $xmlAttribute->description);
                }

                if (isset($xmlAttribute->type)) {
                    $attribute->type = DataType::getDataType($xmlAttribute->type);
                }

                if (isset($xmlAttribute->required)) {
                    $attribute->required = ((string) $xmlAttribute->required) === 'true';
                }

                $this->attributes[] = $attribute;
            }
        }
    }

    public function toString(): string
    {
        // Generate attributes string
        $attributes = '[';

        if (isset($this->attributes)) {
            foreach ($this->attributes as $value) {
                $attributes .= 'attribute ('
                    . 'id=' . $value->id
                    . ', name=' . $value->name
                    . ', description=' . ((! empty($value->description)) ? $value->description : '')
                    . ', type=' . $value->type->getValue()
                    . ', required=' . ($value->required === true ? 'true' : 'false')
                    . '), ';
            }

            $attributes = rtrim($attributes, ' ');
            $attributes = rtrim($attributes, ',');
        }

        $attributes .= ']';

        return 'TransactionType ['
            . 'id=' . $this->id
            . ', name=' . $this->name
            . ', description=' . $this->description
            . ', archivingDuration=' . $this->archivingDuration
            . ', storeOnly=' . $this->storeOnly
            . ', attributes=' . $attributes
            . ']';
    }

    public function toXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><transaction_type></transaction_type>');

        // Some fields are mandatory, especially when setting data to the API
        if (isset($this->id)) {
            $xml->addChild('id', $this->id);
        }

        if (isset($this->name)) {
            $xml->addChild('name', $this->name);
        }

        if (isset($this->description)) {
            $xml->addChild('description', $this->description);
        }

        if (isset($this->archivingDuration)) {
            $xml->addChild('archivingDuration', $this->archivingDuration);
        }

        if (isset($this->storeOnly)) {
            $xml->addChild('storeOnly', ($this->storeOnly === true || $this->storeOnly === 'true') ? 'true' : 'false');
        }

        if (is_array($this->attributes) && ! empty($this->attributes)) {
            $attributes = $xml->addChild('attributes');

            foreach ($this->attributes as $value) {
                $field = $attributes->addChild('attribute');

                if (! empty($value->id)) {
                    $field->addChild('id', $value->id);
                }

                if (! empty($value->name)) {
                    $field->addChild('name', $value->name);
                }

                if (! empty($value->description)) {
                    $field->addChild('description', $value->description);
                }

                if (! empty($value->type)) {
                    $field->addChild('type', $value->type->getValue());
                }

                if (! empty($value->required)) {
                    $field->addChild('required', ($value->required === true || $value->required === 'true') ? 'true' : 'false');
                }
            }
        }

        return $xml;
    }

    /**
     * sanitize this transaction type by removing the ID of every single attribute and return this object for more fluent programming styles
     *
     * @return TransactionType
     */
    public function sanitize()
    {
        if (is_array($this->attributes) && ! empty($this->attributes)) {
            /** @var AttributeType $value */
            foreach ($this->attributes as $value) {
                $value->id = null;
            }
        }

        return $this;
    }
}
