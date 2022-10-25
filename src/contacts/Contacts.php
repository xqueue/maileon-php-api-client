<?php

namespace de\xqueue\maileon\api\client\contacts;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * A wrapper class for a list of Maileon contacts. To access the contacts
 * contained within this list, iterate this object using foreach (i.e. IteratorAggregate is implemented).
 */
class Contacts extends AbstractXMLWrapper implements \IteratorAggregate
{
    private $contacts;

    /**
     * Creates a new list of contacts.
     *
     * @param array $contacts
     * the contacts in the list of contacts
     */
    public function __construct($contacts = array())
    {
        $this->contacts = $contacts;
    }

    /**
     * Adds a new contact to this list of contacts.
     *
     * @param Contact $contact
     */
    public function addContact($contact)
    {
        array_push($this->contacts, $contact);
    }
    
    /**
     * @return \ArrayIterator
     * an iterator for the contacts in this list of contacts
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->contacts);
    }
    
    /**
     * @return int
     * The number of contacts
     */
    public function getCount()
    {
        return sizeof($this->contacts);
    }

    /**
     * Initialization of the contact from a simple xml element. NOT YET IMPLEMENTED.
     *
     * @param \SimpleXMLElement $xmlElement
     * The xml element that is used to parse the contact list from.
     */
    public function fromXML($xmlElement)
    {
        if ($xmlElement->getName() == "contacts") {
            foreach ($xmlElement->children() as $contactXml) {
                $contact = new Contact();
                $contact->fromXML($contactXml);
                $this->contacts[] = $contact;
            }
        }
    }

    /**
     * Serialization to a simple XML element.
     *
     * @return \SimpleXMLElement
     * Generate a XML element from the contact object.
     */
    public function toXML()
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><contacts></contacts>");
        $contactsDom = dom_import_simplexml($xml);

        foreach ($this->contacts as $contact) {
            $contactDom = dom_import_simplexml($contact->toXML(false));
            $contactsDom->appendChild($contactsDom->ownerDocument->importNode($contactDom, true));
        }

        return new \SimpleXMLElement($contactsDom->ownerDocument->saveXML());
    }

    /**
     * Serialization to a simple XML element as string
     *
     * @return string
     *  The string representation of the XML document for this contact.
     */
    public function toXMLString()
    {
        $xml = $this->toXML();
        return $xml->asXML();
    }

    /**
     * Human readable representation of this wrapper.
     *
     * @return string
     *  A human readable version of the contact list.
     */
    public function toString()
    {
        $result = "[\n";
        foreach ($this->contacts as $contact) {
            $result .= '	' . $contact->toString() . "\n";
        }
        $result .= ']';
        return $result;
    }
}
