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
 * Endpoints (DataExtensionsResource.java / DataExtensionResource.java):
 *
 *   Collection (/dataextensions):
 *     GET    /dataextensions            — paginated list of all extensions
 *     POST   /dataextensions            — create a new extension
 *     GET    /dataextensions/datatypes  — available field data types
 *
 *   Single extension (/dataextensions/{id}):
 *     GET    /dataextensions/{id}               — metadata + field definitions
 *     PUT    /dataextensions/{id}               — update settings / add fields
 *     DELETE /dataextensions/{id}               — delete extension
 *     POST   /dataextensions/{id}?importOption= — import records (INSERT/UPDATE/UPSERT/…)
 *     GET    /dataextensions/{id}/records       — paginated row data
 *     DELETE /dataextensions/{id}/records       — delete all records
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
        return $this->get("dataextensions/$extensionId", [], 'application/json', DataExtension::class);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Create extension
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Create a new data extension.
     *
     * Sends POST /dataextensions with an application/vnd.maileon.api+json body
     * containing the extension settings and optional field definitions.
     *
     * On success the API returns HTTP 201 and the numeric ID of the newly
     * created extension as the response body. Access it with getResult().
     *
     * @param DataExtension $dataExtension Extension definition including name,
     *                                     retention policy, and fields.
     *
     * @return MaileonAPIResult|null Result whose getResult() is the new extension ID (int).
     *
     * @throws MaileonAPIException|Exception On connection or server error.
     */
    public function createDataExtension(DataExtension $dataExtension): ?MaileonAPIResult
    {
        $dataExtension->validate();

        $payload = json_encode(
            $dataExtension->toArray(),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        return $this->post(
            'dataextensions',
            $payload,
            [],
            'application/vnd.maileon.api+json',
            null,
            'application/vnd.maileon.api+json',
            strlen($payload)
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Update extension
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Update an existing data extension's settings and/or add new fields.
     *
     * Sends PUT /dataextensions/{id}. Existing field definitions cannot be
     * removed; only new fields can be added in the same call.
     *
     * Returns HTTP 200 on success with an empty body.
     *
     * @param int           $extensionId   Numeric Maileon data extension ID.
     * @param DataExtension $dataExtension Updated settings and/or new fields to add.
     *                                     The API treats this as a full settings replacement,
     *                                     so $dataExtension->name must always be set.
     *
     * @return MaileonAPIResult|null 200 on success.
     *
     * @throws MaileonAPIException|Exception On connection or server error.
     */
    public function updateDataExtension(int $extensionId, DataExtension $dataExtension): ?MaileonAPIResult
    {
        $dataExtension->validate();

        $payload = json_encode(
            $dataExtension->toArray(),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        return $this->put(
            "dataextensions/$extensionId",
            $payload,
            [],
            'application/json'
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Delete extension
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Delete a data extension by ID.
     *
     * Sends DELETE /dataextensions/{id}. Returns HTTP 200 on success.
     * The API returns a 400 error when the extension has active usages
     * (e.g. referenced in a mailing or automation).
     *
     * @param int $extensionId Numeric Maileon data extension ID.
     *
     * @return MaileonAPIResult|null 200 on success.
     *
     * @throws MaileonAPIException|Exception On connection or server error.
     */
    public function deleteDataExtension(int $extensionId): ?MaileonAPIResult
    {
        return $this->delete("dataextensions/$extensionId", [], 'application/json');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Available field data types
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Return the list of field data type names supported by Maileon.
     *
     * Sends GET /dataextensions/datatypes. The response is a JSON array of
     * strings. The API currently returns the 9 basic types:
     * ["string","double","float","integer","boolean","date","timestamp","contact_email","contact_external_id"].
     *
     * Access the result with MaileonAPIResult::getResult() which returns string[].
     *
     * @return MaileonAPIResult|null Result whose getResult() is string[].
     *
     * @throws MaileonAPIException|Exception On connection or server error.
     */
    public function getDataTypes(): ?MaileonAPIResult
    {
        return $this->get('dataextensions/datatypes', [], 'application/vnd.maileon.api+json', ['array', null]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Write records (upsert / synchronize)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Import records into a data extension.
     *
     * Sends POST /dataextensions/{id}?importOption={option}.
     *
     * The API requires a DataRecords envelope with two keys:
     *   field_names  — ordered list of field name strings
     *   records_list — list of {"values": [...]} objects, one per row,
     *                  values ordered to match field_names
     *
     * Valid importOption values: INSERT, UPDATE, UPSERT,
     *   INSERT_IGNORE_DUPLICATES, DELETE.
     *
     * @param int                     $extensionId  Numeric Maileon data extension ID.
     * @param DataExtensionRecord[]   $records      Rows to import.
     * @param string                  $importOption One of the valid import modes.
     *
     * @return MaileonAPIResult|null 201 on success; inspect getStatusCode() on failure.
     *
     * @throws MaileonAPIException|Exception On connection or server error.
     */
    public function synchronizeRecords(int $extensionId, array $records, string $importOption = 'UPSERT'): ?MaileonAPIResult
    {
        if (empty($records)) {
            return null;
        }

        $fieldNames  = reset($records)->getFieldNames();
        $recordsList = array_map(
            fn(DataExtensionRecord $record) => ['values' => array_map(
                fn(string $name) => $record->has($name) ? (string) $record->get($name) : '',
                $fieldNames
            )],
            $records
        );

        $payload = json_encode(
            ['field_names' => $fieldNames, 'records_list' => $recordsList],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        return $this->post(
            "dataextensions/$extensionId",
            $payload,
            ['importOption' => $importOption],
            'application/json',
            null,
            'application/json',
            strlen($payload)
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Delete all records
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Delete all records of a data extension.
     *
     * Sends DELETE /dataextensions/{id}/records?authorized=yes.
     *
     * The `authorized=yes` query parameter is required by the API to prevent
     * accidental bulk deletion. Returns HTTP 200 on success.
     *
     * @param int $extensionId Numeric Maileon data extension ID.
     *
     * @return MaileonAPIResult|null 200 on success.
     *
     * @throws MaileonAPIException|Exception On connection or server error.
     */
    public function deleteAllRecords(int $extensionId): ?MaileonAPIResult
    {
        return $this->delete(
            "dataextensions/$extensionId/records",
            ['authorized' => 'yes'],
            'application/json'
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
