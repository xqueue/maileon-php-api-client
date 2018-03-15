<?php

namespace XQueue\Maileon\API\ContactFilters;

use XQueue\Maileon\API\XML\AbstractXMLWrapper;
use XQueue\Maileon\API\ContactFilters\Rule;

/**
 * The wrapper class for a Maileon contact filter.
 *
 * @author Felix Heinrichs | Trusted Mails GmbH | <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus St&auml;nder | Trusted Mails GmbH | <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class ContactFilter extends AbstractXMLWrapper
{
    // TODO document fields
    public $author;
    public $countContacts;
    public $countRules;
    public $created;
    public $id;
    public $name;
    public $state;
    public $rules;

    // TODO document arguments
    /**
     * Creates a new contact filter wrapper object.
     *
     * @param number $id
     * @param string $name
     * @param string $author
     * @param number $countContacts
     * @param number $countRules
     * @param string $created
     * @param string $state
     */
    function __construct(
        $id = 0,
        $name = "",
        $author = "",
        $countContacts = 0,
        $countRules = 0,
        $created = "1970-01-01 00:00:00",
        $state = "")
    {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
        $this->countContacts = $countContacts;
        $this->countRules = $countRules;
        $this->created = $created;
        $this->state = $state;
    }

    /**
     * Adds a rule to the contact filter
     *
     * @param com_maileon_api_contactfilters_Rule $rule
     */
    function addRule($rule)
    {
        if (!$this->rules) $this->rules = array();
        array_push($this->rules, $rule);
    }

    /**
     * Initializes this contact filter from an XML representation.
     *
     * @param SimpleXMLElement $xmlElement
     *  the XML representation to use
     */
    function fromXML($xmlElement)
    {
        $this->author = $xmlElement->author;
        $this->countContacts = $xmlElement->count_contacts;
        $this->countRules = $xmlElement->count_rules;
        $this->created = $xmlElement->created;
        $this->id = $xmlElement->id;
        $this->name = $xmlElement->name;
        $this->state = $xmlElement->state;
        if ($xmlElement->rules) {
            $rules = $xmlElement->rules;
            foreach ($rules as $rule) {
                array_push($this->rules, new Rule($rule->is_customfield, $rule->field, $rule->operator, $rule->value, $rule->type));
            }
        }
    }

    /**
     * @return \em SimpleXMLElement
     *  containing the serialized representation of this contact filter
     */
    function toXML()
    {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><contactfilter></contactfilter>");

        $xml->addChild("id", $this->id);
        $xml->addChild("name", $this->name);
        $xml->addChild("author", $this->author);
        $xml->addChild("count_contacts", $this->countContacts);
        $xml->addChild("count_rules", $this->countRules);
        $xml->addChild("created", $this->created);
        $xml->addChild("state", $this->state);

        if (isset($this->rules)) {
            $rules = $xml->addChild("rules");
            foreach ($this->rules as $rule) {
                $field = $rules->addChild("rule");
                $field->addChild("is_customfield", ($rule->isCustomfield) ? "true" : "false");
                $field->addChild("field", $rule->field);
                $field->addChild("operator", $rule->operator);
                $field->addChild("value", $rule->value);
                $field->addChild("type", $rule->type);
            }
        }

        return $xml;
    }

    /**
     * @return \em string
     *  containing a human-readable representation of this contact filter
     */
    function toString()
    {

        // Generate standard field string
        $rules = "";
        if (isset($this->rules)) {
            foreach ($this->rules as $rule) {
                $rules .= $rule->toString() . ",";
            }
            $rules = rtrim($rules, ',');
        }

        return "ContactFilter [author=" . $this->author . ", countContacts="
        . $this->countContacts . ", countRules=" . $this->countRules . ", created="
        . $this->created . ", id=" . $this->id . ", name=" . $this->name . ", state="
        . $this->state . ", rules={" . $rules . "}]";
    }
}