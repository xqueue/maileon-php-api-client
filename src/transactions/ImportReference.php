<?php

namespace de\xqueue\maileon\api\client\transactions;

/**
 * A class for wrapping import references.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class ImportReference
{
    /**
     * An array identifying the contact that contains at least one of the following
     * attributes: id/external_id/email and permission
     *
     * @var ImportContactReference
     */
    public $contact;

    public function toString(): string
    {
        if (! empty($this->contact)) {
            return 'ImportReference [' . $this->contact->toString() . ']';
        }

        return 'ImportReference []';
    }
}
