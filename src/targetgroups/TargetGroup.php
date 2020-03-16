<?php

namespace de\xqueue\maileon\api\client\targetgroups;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for a Maileon target group.
 */
class TargetGroup extends AbstractXMLWrapper
{

    public $id;
    public $name;
    public $author;
    public $state;
    public $type;
    public $contactFilterName;
    public $contactFilterId;
    public $evaluated;
    public $created;
    public $updated;
    public $countActiveContacts;
    public $countContacts;

    /**
     * Creates a new target group wrapper object.
     *
     * @param number $id
     * @param string $name
     * @param string $author
     * @param string $state
     * @param string $type
     * @param string $contactFilterName
     * @param number $contactFilterId
     * @param string $evaluated
     * @param string $created
     * @param string $updated
     * @param number $countActiveContacts
     * @param number $countContacts
     */
    public function __construct(
        $id = 0,
        $name = "",
        $author = "",
        $state = "",
        $type = "",
        $contactFilterName = "",
        $contactFilterId = 0,
        $evaluated = "1970-01-01 00:00:00",
        $created = "1970-01-01 00:00:00",
        $updated = "1970-01-01 00:00:00",
        $countActiveContacts = 0,
        $countContacts = 0
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
        $this->state = $state;
        $this->type = $type;
        $this->contactFilterName = $contactFilterName;
        $this->contactFilterId = $contactFilterId;
        $this->evaluated = $evaluated;
        $this->created = $created;
        $this->updated = $updated;
        $this->countActiveContacts = $countActiveContacts;
        $this->countContacts = $countContacts;
    }

    /**
     * Initializes this target group from an XML representation.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the XML representation to use
     */
    public function fromXML($xmlElement)
    {
        $this->id = $xmlElement->id;
        $this->name = $xmlElement->name;
        $this->author = $xmlElement->author;
        $this->state = $xmlElement->state;
        $this->type = $xmlElement->type;
        $this->contactFilterName = $xmlElement->contact_filter_name;
        $this->contactFilterId = $xmlElement->contact_filter_id;
        $this->evaluated = $xmlElement->evaluated;
        $this->created = $xmlElement->created;
        $this->updated = $xmlElement->updated;
        $this->countActiveContacts = $xmlElement->count_active_contacts;
        $this->countContacts = $xmlElement->count_contacts;
    }

    /**
     * @return \SimpleXMLElement
     *  containing the serialized representation of this target group
     */
    public function toXML()
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><targetgroup></targetgroup>");

        $xml->addChild("id", $this->id);
        $xml->addChild("name", $this->name);
        $xml->addChild("author", $this->author);
        $xml->addChild("state", $this->state);
        $xml->addChild("type", $this->type);
        $xml->addChild("contact_filter_name", $this->contactFilterName);
        $xml->addChild("contact_filter_id", $this->contactFilterId);
        $xml->addChild("evaluated", $this->evaluated);
        $xml->addChild("created", $this->created);
        $xml->addChild("updated", $this->updated);
        $xml->addChild("count_active_contacts", $this->countActiveContacts);
        $xml->addChild("count_contacts", $this->countContacts);

        return $xml;
    }

    /**
     * Serialization to a simple XML element as string
     *
     * @return string
     *  The string representation of the XML document.
     */
    public function toXMLString()
    {
        $xml = $this->toXML();
        return $xml->asXML();
    }

    /**
     * @return string
     *  containing a human-readable representation of this target group
     */
    public function toString()
    {
        return "ContactFilter [" .
        "id=" . $this->id .
        ", name=" . $this->name .
        ", author=" . $this->author .
        ", state=" . $this->state .
        ", type=" . $this->type .
        ", contactFilterName=" . $this->contactFilterName .
        ", contactFilterId=" . $this->contactFilterId .
        ", evaluated=" . $this->evaluated .
        ", created=" . $this->created .
        ", updated=" . $this->updated .
        ", countActiveContacts=" . $this->countActiveContacts .
        ", countContacts=" . $this->countContacts .
        "]";
    }
}
