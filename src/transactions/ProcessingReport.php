<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A wrapper class for a single transaction processing report
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
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
     * Whether this transaction was successfully queued
     *
     * @var boolean
     */
    public $queued;

    /**
     * The identifier of the transaction.
     * Available when generated automatically (generate_transaction_id=true) or generated externally
     *
     * @var string
     */
    public $transaction_id;

    /**
     * The error message (if there was any)
     *
     * @var string
     */
    public $message;

    public function __construct()
    {
        // parent::__construct() ?

        $this->contact = new ReportContact();
    }
}
