<?php

declare(strict_types=1);

namespace de\xqueue\maileon\api\client\dataextensions;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;

/**
 * Facade for the Maileon Data Extensions REST API.
 *
 * Confirmed endpoints from DataExtensionsResource.java / DataExtensionResource.java:
 *   GET  /dataextensions                    — paginated list of all data extensions
 *   GET  /dataextensions/{id}               — single extension with full field definitions
 *   GET  /dataextensions/{id}/records       — paginated row data, one JSON object per row
 *
 * All responses are deserialised into typed PHP objects:
 *   listDataExtensions()      → MaileonAPIResult::getResult() is DataExtensionSummary[]
 *   getDataExtension()        → MaileonAPIResult::getResult() is DataExtension
 *   getDataExtensionRecords() → MaileonAPIResult::getResult() is DataExtensionRecord[]
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class DataExtensionsService extends AbstractMaileonService
{
    // ──────────────────────────────────────────────────────────────────────────
    // Collection: list all extensions
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Return one page of data extension summaries.
     *
     * Each summary contains: id, name, description, retention_policy, created_user,
     * created (ISO 8601), count_fields, count_records.
     *
     * The result is automatically deserialised into an array of DataExtensionSummary
     * objects via JSONDeserializer. Access them with MaileonAPIResult::getResult().
     *
     * @param int $page_index 1-based page number (API default: 1).
     * @param int $page_size  Results per page (API default: 100, max: 1000).
     *
     * @return MaileonAPIResult|null Result whose getResult() is DataExtensionSummary[].
     *
     * @throws MaileonAPIException|Exception On connection or server error.
     */
    public function listDataExtensions(int $page_index = 1, int $page_size = 100): ?MaileonAPIResult
    {
        return $this->get(
            'dataextensions',
            [
                'page_index' => $page_index,
                'page_size'  => $page_size,
            ],
            'application/vnd.maileon.api+json',
            ['array', DataExtensionSummary::class]
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Single extension: metadata + field definitions
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Fetch metadata and full field definitions for one data extension.
     *
     * The result is deserialised into a DataExtension object including its
     * nested DataExtensionField[] array. Access it with MaileonAPIResult::getResult().
     *
     * Response fields: name, description, retention_policy, delete_date,
     * delete_interval, delete_interval_unit, fields[].
     *
     * @param int $extensionId Numeric Maileon data extension ID.
     *
     * @return MaileonAPIResult|null Result whose getResult() is a DataExtension.
     *
     * @throws MaileonAPIException|Exception On connection or server error.
     */
    public function getDataExtension(int $extensionId): ?MaileonAPIResult
    {
        return $this->get(
            "dataextensions/$extensionId",
            [],
            'application/vnd.maileon.api+json',
            DataExtension::class
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Records (row data)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Fetch one page of records from a data extension.
     *
     * The API sorts by internal record ID. Each record is deserialised into a
     * DataExtensionRecord whose values map holds all field-name => value pairs.
     * Null field values are omitted from the API response.
     *
     * Access the result with MaileonAPIResult::getResult() which returns DataExtensionRecord[].
     *
     * @param int      $extensionId Numeric Maileon data extension ID.
     * @param int      $page_index  1-based page number.
     * @param int      $page_size   Records per page (max 1000 as per API limits).
     * @param bool     $sort_asc    Sort direction; true = ascending by record ID.
     * @param string[] $fields      If non-empty, only these fields are returned.
     *                              Omit to return all fields.
     *
     * @return MaileonAPIResult|null Result whose getResult() is DataExtensionRecord[].
     *
     * @throws MaileonAPIException|Exception On connection or server error.
     */
    public function getDataExtensionRecords(
        int   $extensionId,
        int   $page_index = 1,
        int   $page_size = 1000,
        bool  $sort_asc = true,
        array $fields = []
    ): ?MaileonAPIResult {
        $query = [
            'page_index' => $page_index,
            'page_size'  => $page_size,
            'sort_asc'   => $sort_asc ? 'true' : 'false',
        ];

        // The API accepts repeated `fields` query params: ?fields=foo&fields=bar
        // AbstractMaileonService handles array values as repeated params.
        if (!empty($fields)) {
            $query['fields'] = $fields;
        }

        return $this->get(
            "dataextensions/$extensionId/records",
            $query,
            'application/json',
            ['array', DataExtensionRecord::class]
        );
    }
}
