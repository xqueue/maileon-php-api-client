<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A wrapper class for a single transaction processing report
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class ProcessingReport extends AbstractJSONWrapper
{
    /**
     * The contact this transaction was sent to
     *
     * @var ReportContact
     */
    public $contact;
    
    /**
     * Whether this transaction was succesfully queued
     *
     * @var boolean
     */
    public $queued;
    
    /**
     * The error message (if there was any)
     *
     * @var string
     */
    public $message;
    
    public function __construct()
    {
        $this->contact = new ReportContact();
    }
}
