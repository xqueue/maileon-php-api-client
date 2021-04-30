<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\json\JSONSerializer;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use de\xqueue\maileon\api\client\AbstractMaileonService;

/**
 * Facade that wraps the REST service for transactions.
 *
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 * @author Marcus St&auml;nder | Trusted Technologies GmbH |
 * <a href="mailto:marcus.staender@trusted-technologies.de">marcus.staender@trusted-technologies.de</a>
 */

class TransactionsService extends AbstractMaileonService
{
    /**
     * @return MaileonAPIResult
     * the result object of the API call, with the count of transaction types available
     * at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     *      if there was a connection problem or a server error occurred
     */
    public function getTransactionTypesCount()
    {
        return $this->get('transactions/types/count');
    }
        
    /**
     * Gets the TransactionTypes defined in the system.
     *
     * @param number $page_index
     * the paging index number
     * @param number $page_size
     * the number of results per page
     * @return MaileonAPIResult
     * the result object of the API call, with a com_maileon_api_transactions_TransactionType[]
     * available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function getTransactionTypes($page_index = 1, $page_size = 10)
    {
        $queryParameters = array(
                'page_index' => $page_index,
                'page_size' => $page_size
        );

        return $this->get('transactions/types', $queryParameters);
    }
    
    /**
     * Gets information about a transaction type.
     *
     * @param integer $id
     * the id of the transaction type to get information about
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     *      if there was a connection problem or a server error occurred
     */
    public function getTransactionType($id)
    {
        return $this->get("transactions/types/" . $id);
    }
    
    /**
     * Gets information about a transaction type by its name.
     *
     * @param string $name
     * the name of the transaction type to get information about
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     *      if there was a connection problem or a server error occurred
     */
    public function getTransactionTypeByName($name)
    {
        return $this->get("transactions/types/" . urlencode($name));
    }
        
    /**
     * Creates a new contact event type.
     *
     * @param TransactionType $trt
     * the TransactionType defining the new transaction type to create
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     *      if there was a connection problem or a server error occurred
     */
    public function createTransactionType($trt)
    {
        return $this->post("transactions/types", $trt -> toXMLString());
    }
        
    /**
     * Deletes a transaction type from the system.
     *
     * @param integer $id
     * the id of the transaction type to delete
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function deleteTransactionType($id)
    {
        return $this->delete("transactions/types/" . $id);
    }
    
    /**
     * Deletes a transaction type from the system by name.
     *
     * @param string $name
     * the name of the transaction type to delete
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function deleteTransactionTypeByName($name)
    {
        return $this->delete("transactions/types/" . urlencode($name));
    }
        
    /**
     * Creates a transaction
     *
     * @param array $transactions
     * an array of Transaction objects
     * @param boolean $release
     * Deprecated parameter that is not used anymore
     * @param boolean $ignoreInvalidEvents
     * If set to false, exceptions like invalid contacts will cause the service to return 400 Bad request.
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function createTransactions($transactions, $release = true, $ignoreInvalidEvents = false)
    {
        $queryParameters = array(
            'ignore_invalid_transactions' => ($ignoreInvalidEvents == true)?'true':'false'
        );

        $data = JSONSerializer::json_encode($transactions);
        
        $result = $this->post(
            "transactions",
            $data,
            $queryParameters,
            "application/json",
            'ProcessingReports'
        );
        
        return $result;
    }
        
    /**
     * Delete all transactions of a given type before a given date in the account.
     * Any previously-released transactions will be ignored.
     *
     * @param integer $type_id
     * the transaction type id of the transactions to delete
     * @param number $before_timestamp
     * the timestamp to compare against, in milliseconds since the start of the UNIX Epoch
     * (1970-01-01 00:00:00)
     * @return MaileonAPIResult
     * the result object of the API call
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function deleteTransactions($type_id, $before_timestamp = 9223372036854775807)
    {
        $queryParameters = array(
            'type_id' => $type_id,
            'before_timestamp' => $before_timestamp
        );
                
        return $this->delete("transactions", $queryParameters);
    }

    /**
     * Finds a transaction type by name
     *
     * @param string $type_name
     * the transaction name to find
     * @return int
     * the id if the found transaction
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     * @deprecated $name can be used as ID, @see getTransactionType()
     */
    public function findTransactionTypeByName($type_name)
    {
        return $this->getTransactionType($type_name);
    }
        
    /**
    * Gets the last $count transaction events of a given transaction type.
    *
    * @param int $type_id
    *  the ID of the transaction type to get transaction events for
    * @param int $count
    *  The number of last transactions to get. Valid range: [1..2000]
    */
    public function getRecentTransactions($type_id, $count = 1000, $minExcludedTxId = 0)
    {
        if ($count < 1 || $count > 2000) {
            throw new MaileonAPIException("the given count is not in the [1..2000] range");
        }
        if ($minExcludedTxId < 0) {
            throw new MaileonAPIException("the given $minExcludedTxId must be greater or equal to 0");
        }

        $queryParameters = array(
            'type_id' => $type_id,
            'count' => $count,
            'min_excluded_transaction_id' => $minExcludedTxId
        );

        return $this->get(
            "transactions",
            $queryParameters,
            "application/json",
            array('array', 'de\xqueue\maileon\api\client\transactions\RecentTransaction')
        );
    }
}
