<?php

namespace XQueue\Maileon\API\Transactions;

use XQueue\Maileon\API\JSON\AbstractJSONWrapper;

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
     * @var com_maileon_api_contacts_Permission 
     *  the permission of this contact
     */
    public $permission;
    
    /**
     * @return \em string
     *    a human-readable representation of this ContactReference
     */
    function __toString() {
        return parent::__toString();
    }
    
    /**
     * Signals to the JSON serializer whether this object should be serialized
     * 
     * @return boolean
     */
    function isEmpty() {
        $result = !isset($this->id) && !isset($this->external_id) && !isset($this->email);
        
        return $result;
    }
    
    function toArray() {
        $array = parent::toArray();
        
        if($this->permission != null) {
            $array['permission'] = $this->permission->code;
        }
        
        return $array;
    }
}
