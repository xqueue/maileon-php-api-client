<?php

namespace de\xqueue\maileon\api\client\transactions;

/**
 * A class for wrapping import references.
 *
 * @author Marcus Staender
 */
class ImportReference
{
    /**
     *
     * @var ImportContactReference
     *  an array identifying the contact that contains at least one of the following
     *  attributes: id/external_id/email and permission
     */
    public $contact;
    /**
     * @return string
     *    a human-readable representation of this ContactReference
     */
    public function toString()
    {

        if (!empty($this->contact)) {
            return "ImportReference [" . $this->contact->toString() . "]";
        } else {
            return "ImportReference []";
        }
    }
}
