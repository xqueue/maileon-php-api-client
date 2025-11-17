<?php

namespace de\xqueue\maileon\api\client\transactions;

/**
 * A class for wrapping contact references.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 */
class ImportContactReference
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
     * The permission of the contact if it should be created
     *
     * @var string
     */
    public $permission;

    public function toString(): string
    {
        if (! empty($this->permission)) {
            $permissionCode = $this->permission->getCode();
        } else {
            $permissionCode = -1;
        }

        if (! empty($this->id)) {
            return 'ImportContactReference ['
                . 'id=' . $this->id
                . ', permission=' . $permissionCode
                . ']';
        }

        if (! empty($this->email)) {
            return 'ImportContactReference ['
                . 'email=' . $this->email
                . ', permission=' . $permissionCode
                . ']';
        }

        if (! empty($this->external_id)) {
            return 'ImportContactReference ['
                . 'external_id=' . $this->external_id
                . ', permission=' . $permissionCode
                . ']';
        }

        return 'ImportContactReference ['
            . 'permission=' . $permissionCode
            . ']';
    }
}
