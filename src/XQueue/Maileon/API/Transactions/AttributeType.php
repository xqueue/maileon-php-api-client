<?php

namespace XQueue\Maileon\API\Transactions;

/**
 * A maileon attribute type
 *
 * @author Marcus St&auml;nder | Trusted Technologies GmbH | <a href="mailto:marcus.staender@trusted-technologies.de">marcus.staender@trusted-technologies.de</a>
 */
class AttributeType
{
    /**
     *
     * @var integer The id of this attribute
     */
    public $id;
    /**
     *
     * @var string The name of this attribute
     */
    public $name;
    /**
     *
     * @var com_maileon_api_transactions_DataType The type of this attribute
     */
    public $type;
    /**
     *
     * @var boolean Whether the given attribute is required
     */
    public $required;

    /**
     * Creates a new AttributeType.
     *
     * @param integer $id
     *    the id of the attribute
     * @param string $name
     *    the name of the attribute
     * @param com_maileon_api_transactions_DataType $type
     *    the type of the attribute's value
     * @param bool $required
     *    set to true if this attribute is required, false if it is optional
     */
    function __construct($id, $name, $type, $required)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
    }
}