<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A class for wrapping contact references.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 */
class ContactReference extends AbstractJSONWrapper
{
    /**
     * The Maileon ID of the contact to send the transaction to
     *
     * @var int
     */
    public $id;

    /**
     * The external ID of the contact to send the transaction to
     *
     * @var string
     */
    public $external_id;

    /**
     * The email address of the contact to send the transaction to
     *
     * @var string
     */
    public $email;

    /**
     * The permission of this contact
     *
     * @var Permission
     */
    public $permission;

    /**
     * Signals to the JSON serializer whether this object should be serialized
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return ! isset($this->id) && ! isset($this->external_id) && ! isset($this->email);
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        if ($this->permission != null) {
            $array['permission'] = $this->permission->code;
        }

        return $array;
    }
}
