<?php

namespace XQueue\Maileon\API\ContactEvents;

/**
 * A maileon attribute type
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class AttributeType
{
    public $name;
    public $dataType;
    public $description;
    public $required;

    /**
     * Creates a new AttributeType.
     *
     * @param string $name
     *    the name of the attribute
     * @param com_maileon_api_contactevents_DataType $dataType
     *    the type of the attribute's value
     * @param string $description
     *    the meaning of this attribute
     * @param bool $required
     *    set to true if this attribute is required, false if it is optional
     */
    function __construct($name, $dataType, $description, $required)
    {
        $this->name = $name;
        $this->datatype = $dataType;
        $this->description = $description;
        $this->required = $required;
    }
}