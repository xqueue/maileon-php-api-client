<?php

namespace de\xqueue\maileon\api\client\transactions;

/**
 * A maileon attribute type
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class AttributeType
{
    /**
     * The id of this attribute
     *
     * @var int
     */
    public $id;

    /**
     * The name of this attribute
     *
     * @var string
     */
    public $name;

    /**
     * The description of this attribute
     *
     * @var string
     */
    public $description;

    /**
     * The type of this attribute
     *
     * @var DataType
     */
    public $type;

    /**
     * Whether the given attribute is required
     *
     * @var boolean
     */
    public $required;

    /**
     * Creates a new AttributeType.
     *
     * @param int      $id          the id of the attribute
     * @param string   $name        the name of the attribute
     * @param DataType $type        the type of the attribute's value
     * @param bool     $required    set to true if this attribute is required, false if it is optional
     * @param string   $description the description of the attribute
     */
    public function __construct(
        $id = null,
        $name = '',
        $type = '',
        $required = false,
        $description = ''
    ) {
        $this->id          = $id;
        $this->name        = $name;
        $this->type        = $type;
        $this->required    = $required;
        $this->description = $description;
    }
}
