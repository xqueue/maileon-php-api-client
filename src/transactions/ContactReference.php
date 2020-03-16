<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;
use de\xqueue\maileon\api\client\contacts\Permission;

/**
 * A class for wrapping contact references.
 *
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 */
class ContactReference extends AbstractJSONWrapper
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
     * @var Permission
     *  the permission of this contact
     */
    public $permission;
    
    /**
     * @return string
     *    a human-readable representation of this ContactReference
     */
    public function toString()
    {
        return parent::__toString();
    }
    
    /**
     * Signals to the JSON serializer whether this object should be serialized
     *
     * @return boolean
     */
    public function isEmpty()
    {
        $result = !isset($this->id) && !isset($this->external_id) && !isset($this->email);
        
        return $result;
    }
    
    public function toArray()
    {
        $array = parent::toArray();
        
        if ($this->permission != null) {
            $array['permission'] = $this->permission->code;
        }
        
        return $array;
    }
}
