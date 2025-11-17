<?php

namespace de\xqueue\maileon\api\client\contactfilters;

/**
 * The wrapper class for a contact filter rule
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Rule
{
    public $isCustomfield;
    public $field;
    public $operator;
    public $value;
    public $type;

    /**
     * Constructor initializing rule
     *
     * @param bool   $isCustomfield
     * @param string $field
     * @param string $operator The operator that should be used. Best: use EQUALS, then STARTS_WITH, then, if not possible with these,
     *                         use others. Valid: EQUALS, NOTEQUALS, CONTAINS, NOTCONTAINS, STARTS_WITH
     * @param string $value
     */
    public function __construct(
        $isCustomfield,
        $field,
        $operator,
        $value,
        $type = 'string'
    ) {
        $this->isCustomfield = $isCustomfield;
        $this->field         = $field;
        $this->operator      = $operator;
        $this->value         = $value;
        $this->type          = $type;
    }

    public function toString(): string
    {
        return 'Rule ['
            . 'isCustomfield=' . ($this->isCustomfield ? 'true' : 'false')
            . ', field=' . $this->field
            . ', operator=' . $this->operator
            . ', value=' . $this->value
            . ', type = ' . $this->type
            . ']';
    }
}
