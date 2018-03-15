<?php

namespace XQueue\Maileon\API\ContactFilters;

/**
 * The wrapper class for a contact filter rule
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
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
     * @param bool $isCustomfield
     * @param string $field
     * @param string $operator The operator that should be used. Best: use EQUALS, then STARTS_WITH, then, if not possible with these, use others. Valid: EQUALS, NOTEQUALS, CONTAINS, NOTCONTAINS, STARTS_WITH
     * @param string $value
     */
    function __construct($isCustomfield, $field, $operator, $value, $type = "string")
    {
        $this->isCustomfield = $isCustomfield;
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * Human readable representation of this rule.
     *
     * @return \em string
     *  A human readable version of the rule.
     */
    function toString()
    {
        return "Rule [isCustomfield=" . ($this->isCustomfield) ? "true" : "false" . ", field=" . $this->field . ", operator=" . $this->operator . ", value=" . $this->value . " (type = " . $this->type . ")";
    }

}