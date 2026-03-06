<?php

namespace de\xqueue\maileon\api\client\contacts;

use ArrayIterator;
use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use IteratorAggregate;
use SimpleXMLElement;
use Traversable;

use function count;
use function dom_import_simplexml;

/**
 * A wrapper class for a list of Maileon contacts. To access the contacts
 * contained within this list, iterate this object using foreach (i.e. IteratorAggregate is implemented).
 */
class Contacts extends AbstractXMLWrapper implements IteratorAggregate
{
    private $contacts;

    /**
     * Creates a new list of contacts.
     *
     * @param array $contacts the contacts in the list of contacts
     */
    public function __construct($contacts = [])
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
        $this->contacts[] = $contact;
    }

    /**
     * @return ArrayIterator an iterator for the contacts in this list of contacts
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->contacts);
    }

    /**
     * @return int The number of contacts
     */
    public function getCount(): int
    {
        return count($this->contacts);
    }

    public function fromXML($xmlElement)
    {
        if ($xmlElement->getName() === 'contacts') {
            foreach ($xmlElement->children() as $contactXml) {
                $contact = new Contact();
                $contact->fromXML($contactXml);
                $this->contacts[] = $contact;
            }
        }
    }

    public function toXML()
    {
        $xml         = new SimpleXMLElement('<?xml version="1.0"?><contacts></contacts>');
        $contactsDom = dom_import_simplexml($xml);

        foreach ($this->contacts as $contact) {
            $contactDom = dom_import_simplexml($contact->toXML(false));
            $contactsDom->appendChild($contactsDom->ownerDocument->importNode($contactDom, true));
        }

        return new SimpleXMLElement($contactsDom->ownerDocument->saveXML());
    }

    public function toString(): string
    {
        $result = "[\n";

        foreach ($this->contacts as $contact) {
            $result .= '	' . $contact->toString() . "\n";
        }

        $result .= ']';

        return $result;
    }
}
