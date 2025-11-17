<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A class for wrapping a recent transaction.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 */
class RecentTransaction extends AbstractJSONWrapper
{
    /**
     * The data for the transaction
     *
     * @var array
     */
    public $tx;

    /**
     * The id for this data
     *
     * @var int
     */
    public $txId;

    /**
     * The contact id
     *
     * @var int
     */
    public $contactId;

    /**
     * The email address
     *
     * @var string
     */
    public $email;
}
