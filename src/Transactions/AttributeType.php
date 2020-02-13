<?php

namespace Maileon\Transactions;

/**
 * A maileon attribute type
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
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
     * @var string The description of this attribute
     */
    public $description;
    /**
     *
     * @var DataType The type of this attribute
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
     * @param DataType $type
     *    the type of the attribute's value
     * @param bool $required
     *    set to true if this attribute is required, false if it is optional
     * @param string $description
     *    the description of the attribute
     */
    public function __construct($id, $name, $type, $required, $description = "")
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->description = $description;
    }
}
