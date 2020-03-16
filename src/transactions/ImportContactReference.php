<?php

namespace de\xqueue\maileon\api\client\transactions;

/**
 * A class for wrapping contact references.
 *
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 */
class ImportContactReference
{
    /**
     *
     * @var integer
     *  the Maileon ID of the contact to send the transaction to
     */
    public $id;

    /**
     *
     * @var string
     *  the external ID of the contact to send the transaction to
     */
    public $external_id;

    /**
     *
     * @var string
     *  the email address of the contact to send the transaction to
     */
    public $email;

    /**
     *
     * @var string
     *  the permission of the contact if it should be created
     */
    public $permission;

    /**
     * @return string
     *    a human-readable representation of this ContactReference
     */
    public function toString()
    {
        if (!empty($this->permission)) {
            $permissionCode = $this->permission->getCode();
        } else {
            $permissionCode = -1;
        }

        if (!empty($this->id)) {
            return "ImportContactReference [id=" . $this->id . ", permission=" . $permissionCode . "]";
        } elseif (!empty($this->email)) {
            return "ImportContactReference [email=" . $this->email . ", permission=" . $permissionCode . "]";
        } elseif (!empty($this->external_id)) {
            return "ImportContactReference [external_id=" .
                $this->external_id . "], permission=" . $permissionCode . "";
        } else {
            return "ImportContactReference [permission=" . $permissionCode . "]";
        }
    }
}
