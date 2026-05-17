<?php

declare(strict_types=1);

namespace de\xqueue\maileon\api\client\dataextensions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * Full representation of a Maileon data extension, including its field definitions.
 *
 * Returned by:
 *   GET /dataextensions/{id}
 *
 * This object is richer than DataExtensionSummary: it contains the complete
 * list of field definitions (fields[]) but does NOT include id, count_records,
 * or created_user — those are only available on the list endpoint.
 *
 * Example JSON (abbreviated):
 * {
 *   "name": "Newsletter subscribers",
 *   "description": "Active opt-in contacts",
 *   "retention_policy": "FOREVER",
 *   "delete_date": null,
 *   "delete_interval": null,
 *   "delete_interval_unit": null,
 *   "fields": [
 *     { "id": 7, "name": "email", "data_type": "STRING", "unique_identifier": true, ... },
 *     { "id": 8, "name": "first_name", "data_type": "STRING", "nullable": true, ... }
 *   ]
 * }
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class DataExtension extends AbstractJSONWrapper
{
    /**
     * Human-readable name of the data extension.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * Optional description of the data extension's purpose.
     *
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Retention policy constant (e.g. "FOREVER", "DAYS_90").
     *
     * @var string|null
     */
    public ?string $retention_policy = null;

    /**
     * Absolute date on which all records will be deleted (ISO 8601).
     * NULL when the extension uses an interval-based policy or FOREVER.
     *
     * @var string|null
     */
    public ?string $delete_date = null;

    /**
     * Number of units after which records are deleted (e.g. 90 for 90 days).
     * NULL when using an absolute delete_date or FOREVER policy.
     *
     * @var int|null
     */
    public ?int $delete_interval = null;

    /**
     * Unit of the delete interval (e.g. "DAYS", "MONTHS").
     * NULL when delete_interval is not set.
     *
     * @var string|null
     */
    public ?string $delete_interval_unit = null;

    /**
     * Ordered list of field definitions for this extension.
     * Populated by fromArray() from the nested "fields" JSON array.
     *
     * @var DataExtensionField[]
     */
    public array $fields = [];

    /**
     * Override fromArray to deserialise the nested "fields" array into
     * DataExtensionField instances instead of raw stdClass objects.
     *
     * All other scalar properties are handled by the parent implementation.
     *
     * @param \stdClass $object_vars Decoded JSON object from json_decode().
     */
    public function fromArray($object_vars): void
    {
        foreach ($object_vars as $key => $value) {
            if ($key === 'fields' && is_array($value)) {
                // Deserialise each field definition into a typed DataExtensionField.
                $this->fields = array_map(
                    static function($fieldData): DataExtensionField {
                        $field = new DataExtensionField();
                        $field->fromArray($fieldData);
                        return $field;
                    },
                    $value
                );
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Override toArray to serialise the fields array as nested arrays rather
     * than relying on AbstractJSONWrapper's generic elementToArray logic.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = parent::toArray();

        // Replace the fields array (which parent would skip or mangle) with
        // properly serialised DataExtensionField arrays.
        if (!empty($this->fields)) {
            $result['fields'] = array_map(
                static fn(DataExtensionField $f): array => $f->toArray(),
                $this->fields
            );
        }

        return $result;
    }

    /**
     * Return all field definitions that are required when inserting a record.
     *
     * @return DataExtensionField[]
     */
    public function getRequiredFields(): array
    {
        return array_values(array_filter($this->fields, static fn(DataExtensionField $f): bool => $f->isRequired()));
    }

    /**
     * Return all field definitions that act as unique identifiers.
     *
     * @return DataExtensionField[]
     */
    public function getUniqueIdentifierFields(): array
    {
        return array_values(array_filter($this->fields, static fn(DataExtensionField $f): bool => $f->unique_identifier === true));
    }

    /**
     * Look up a field definition by name.
     *
     * @param string $name Exact field name to look up.
     *
     * @return DataExtensionField|null The field, or null if not found.
     */
    public function getField(string $name): ?DataExtensionField
    {
        foreach ($this->fields as $field) {
            if ($field->name === $name) {
                return $field;
            }
        }

        return null;
    }
}
