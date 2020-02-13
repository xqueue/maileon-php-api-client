<?php

namespace Maileon\Transactions;

use Maileon\Contacts\Permission;
use Maileon\Json\AbstractJSONWrapper;

/**
 * A wrapper for a transaction report contact
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class ReportContact extends AbstractJSONWrapper
{
    /**
     * The id of this contact
     *
     * @var int
     */
    public $id;
    /**
     * The extrenal id of this contact
     *
     * @var string
     */
    public $external_id;
    /**
     * The email address of this contact
     *
     * @var string
     */
    public $email;
    /**
     * The type string of this contact
     *
     * @var string
     */
    public $type;
    /**
     * The permission of the contact
     *
     * @var Permission
     */
    public $permission;
    /**
     * Whether the contact was created
     *
     * @var boolean
     */
    public $created;
    
    public function fromArray($object_vars)
    {
        parent::fromArray($object_vars);
        
        if ($this->permission !== null) {
            $this->permission = new Permission($this->permission);
        }
    }
}
