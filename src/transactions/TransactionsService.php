<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\json\JSONSerializer;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;

use function mb_convert_encoding;
use function rawurlencode;

/**
 * Facade that wraps the REST service for transactions.
 *
 * @author Viktor Balogh | Maileon Digital Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class TransactionsService extends AbstractMaileonService
{
    /**
     * @return MaileonAPIResult|null The result object of the API call, with the count of transaction types available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTransactionTypesCount()
    {
        return $this->get('transactions/types/count');
    }

    /**
     * Gets the TransactionTypes defined in the system.
     *
     * @param int $page_index the paging index number
     * @param int $page_size  the number of results per page
     *
     * @return MaileonAPIResult|null The result object of the API call, with a TransactionType[] available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTransactionTypes(
        $page_index = 1,
        $page_size = 10
    ) {
        $queryParameters = [
            'page_index' => $page_index,
            'page_size'  => $page_size,
        ];

        return $this->get(
            'transactions/types',
            $queryParameters
        );
    }

    /**
     * Gets information about a transaction type.
     *
     * @param int $id the id of the transaction type to get information about
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTransactionType($id)
    {
        $encodedId = rawurlencode(mb_convert_encoding((string) $id, 'UTF-8'));

        return $this->get("transactions/types/$encodedId");
    }

    /**
     * Gets information about a transaction type by its name.
     *
     * @param string $name the name of the transaction type to get information about
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTransactionTypeByName($name)
    {
        $encodedName = rawurlencode(mb_convert_encoding((string) $name, 'UTF-8'));

        return $this->get("transactions/types/$encodedName");
    }

    /**
     * Creates a new contact event type.
     *
     * @param TransactionType $trt
     * the TransactionType defining the new transaction type to create
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function createTransactionType($trt)
    {
        return $this->post(
            'transactions/types',
            $trt->sanitize()->toXMLString()
        );
    }

    /**
     * Updates an existing transaction type with the given ID in the account.
     * Currently, you can:
     * Add new attributes to transaction type
     * Change the transaction type name
     * Change the transaction type description
     * Change the transaction type archival duration
     * The XML definition is identical to creating a transaction type:
     * https://support.maileon.com/support/create-transaction-type/.
     *
     * Please be aware: due to backwards compatibility of the types with regard to mailings or filters,
     * you cannot change existing attributes or delete them. This means the definition in the update must
     * at least contain all attributes of the original transaction type but might contain 0...n new attributes.
     * However, regarding old attributes, only the name is evaluated, all other attributes are ignored and
     * not processed or even updated.
     *
     * @see https://support.maileon.com/support/update-transaction-type/
     *
     * @param int             $id  the id of the transaction type
     * @param TransactionType $trt the TransactionType defining the new transaction type to create
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function updateTransactionType(
        $id,
        $trt
    ) {
        $encodedId = rawurlencode(mb_convert_encoding((string) $id, 'UTF-8'));

        return $this->put(
            "transactions/types/$encodedId",
            $trt->sanitize()->toXMLString()
        );
    }

    /**
     * Deletes a transaction type from the system.
     *
     * @param int $id the id of the transaction type to delete
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteTransactionType($id)
    {
        $encodedId = rawurlencode(mb_convert_encoding((string) $id, 'UTF-8'));

        return $this->delete("transactions/types/$encodedId");
    }

    /**
     * Deletes a transaction type from the system by name.
     *
     * @param string $name the name of the transaction type to delete
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteTransactionTypeByName($name)
    {
        $encodedName = rawurlencode(mb_convert_encoding((string) $name, 'UTF-8'));

        return $this->delete("transactions/types/$encodedName");
    }

    /**
     * Creates a transaction
     *
     * @param array   $transactions
     * an array of Transaction objects
     * @param boolean $release
     * Deprecated parameter that is not used anymore
     * @param boolean $ignoreInvalidEvents
     * If set to false, exceptions like invalid contacts will cause the service to return 400 Bad request.
     * @param boolean $generateTransactionId
     * If the transaction type contains the special attribute "transaction_id", setting this method to true will
     * generate and fill a random ID in this field and return the ID in the report. This can be used if the ID
     * is not generated externally.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function createTransactions(
        $transactions,
        $release = true,
        $ignoreInvalidEvents = false,
        $generateTransactionId = false
    ) {
        $queryParameters = [
            'ignore_invalid_transactions' => $ignoreInvalidEvents === true ? 'true' : 'false',
            'generate_transaction_id'     => $generateTransactionId === true ? 'true' : 'false',
        ];

        $data = JSONSerializer::json_encode($transactions);

        return $this->post(
            'transactions',
            $data,
            $queryParameters,
            'application/json',
            'ProcessingReports'
        );
    }

    /**
     * Delete all transactions of a given type before a given date in the account.
     * Any previously-released transactions will be ignored.
     *
     * @param int $type_id
     * the transaction type id of the transactions to delete
     * @param int $before_timestamp
     * the timestamp to compare against, in milliseconds since the start of the UNIX Epoch
     * (1970-01-01 00:00:00)
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteTransactions(
        $type_id,
        $before_timestamp = 9223372036854775807
    ) {
        $queryParameters = [
            'type_id'          => $type_id,
            'before_timestamp' => $before_timestamp,
        ];

        return $this->delete(
            'transactions',
            $queryParameters
        );
    }

    /**
     * Finds a transaction type by name
     *
     * @param string $type_name
     * the transaction name to find
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     *
     * @deprecated $name can be used as ID, @see getTransactionType()
     */
    public function findTransactionTypeByName($type_name)
    {
        return $this->getTransactionType($type_name);
    }

    /**
     * Gets the last $count transaction events of a given transaction type.
     *
     * @param int $type_id The ID of the transaction type to get transaction events for
     * @param int $count   The number of last transactions to get. Valid range: [1..2000]
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getRecentTransactions(
        $type_id,
        $count = 1000,
        $minExcludedTxId = 0
    ) {
        if ($count < 1 || $count > 2000) {
            throw new MaileonAPIException('the given count is not in the [1..2000] range');
        }

        if ($minExcludedTxId < 0) {
            throw new MaileonAPIException("the given $minExcludedTxId must be greater or equal to 0");
        }

        $queryParameters = [
            'type_id'                     => $type_id,
            'count'                       => $count,
            'min_excluded_transaction_id' => $minExcludedTxId,
        ];

        return $this->get(
            'transactions',
            $queryParameters,
            'application/json',
            ['array', RecentTransaction::class]
        );
    }

    /**
     * Retrieves the content of a transaction of the given type with the given ID.
     * The ID of the transaction must be specified in a special transaction attribute “transaction_id”.
     *
     * If the transaction_id is generated externally, it is not guaranteed to be unique,
     * in this case Maileon returns the first found result, only. As the same ID might be
     * used in transactions of different transaction types, the type must also be specified.
     *
     * @see https://support.maileon.com/support/create-transaction-type/#articleTOC_4.
     *
     * @param string|int $type_id        The ID of the transaction type.
     * @param string     $transaction_id The transaction ID. If not unique, the first found occurrence will be used.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getTransaction(
        $type_id,
        $transaction_id
    ) {
        $encodedTypeId        = rawurlencode(mb_convert_encoding((string) $type_id, 'UTF-8'));
        $encodedTransactionId = rawurlencode(mb_convert_encoding((string) $transaction_id, 'UTF-8'));

        return $this->get(
            "transactions/$encodedTypeId/transaction_id/$encodedTransactionId",
            [],
            'application/json'
        );
    }

    /**
     * Retrieves the content of a transaction of the given type with the given ID.
     * The ID of the transaction must be specified in a special transaction attribute “transaction_id”.
     *
     * If the transaction_id is generated externally, it is not guaranteed to be unique,
     * in this case Maileon returns the first found result, only. As the same ID might be
     * used in transactions of different transaction types, the type must also be specified.
     *
     * @see https://support.maileon.com/support/create-transaction-type/#articleTOC_4.
     *
     * @param string|int $type_id        The ID of the transaction type.
     * @param string     $transaction_id The transaction ID. If not unique, the first found occurrence will be used.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteTransaction(
        $type_id,
        $transaction_id
    ) {
        $encodedTypeId        = rawurlencode(mb_convert_encoding((string) $type_id, 'UTF-8'));
        $encodedTransactionId = rawurlencode(mb_convert_encoding((string) $transaction_id, 'UTF-8'));

        return $this->delete(
            "transactions/$encodedTypeId/transaction_id/$encodedTransactionId"
            [],
            'application/json'
        );
    }
}
