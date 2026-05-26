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
 *   "retention_policy": "RECORDS_DURATION",
 *   "delete_date": null,
 *   "delete_interval": 7,
 *   "delete_interval_unit": "DAYS",
 *   "fields": [
 *     { "id": 7, "name": "email", "data_type": "contact_email", "unique_identifier": true, ... },
 *     { "id": 8, "name": "first_name", "data_type": "string", "nullable": true, ... }
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
     * Must match the Maileon identifier pattern: [a-zA-Z][a-zA-Z0-9_]{0,38}[a-zA-Z0-9]
     * (letters/digits/underscores only, starts with a letter, 2–40 chars, no hyphens).
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
     * Retention policy. See RetentionPolicy for all valid values.
     * Use RetentionPolicy::NONE for permanent storage (no automatic deletion).
     *
     * @var string|null
     */
    public ?string $retention_policy = null;

    /**
     * Absolute date on which the extension (and its records) will be deleted (ISO 8601).
     * Required when retention_policy is EXTENSION_DATE; null otherwise.
     *
     * @var string|null
     */
    public ?string $delete_date = null;

    /**
     * Number of units after which data is deleted.
     * Required (not null) when retention_policy is EXTENSION_DURATION,
     * EXTENSION_DURATION_RENEW_ON_MODIFICATION, or RECORDS_DURATION.
     * Valid range: 1–60.
     *
     * @var int|null
     */
    public ?int $delete_interval = null;

    /**
     * Unit of the delete interval. See DeleteIntervalUnit for valid values (DAYS, WEEKS, MONTHS).
     * Required when delete_interval is set; null otherwise.
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

    /**
     * Validate all mandatory fields before sending to the API.
     *
     * Required by both createDataExtension and updateDataExtension:
     *   - name
     *   - retention_policy (must be a valid RetentionPolicy constant)
     *   - delete_date       when retention_policy = EXTENSION_DATE
     *   - delete_interval + delete_interval_unit
     *                       when retention_policy is duration-based
     *
     * Also validates every nested DataExtensionField.
     *
     * @throws \InvalidArgumentException on the first missing or invalid value.
     */
    public function validate(): void
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException('DataExtension: name is required.');
        }

        if (empty($this->retention_policy)) {
            throw new \InvalidArgumentException("DataExtension '{$this->name}': retention_policy is required.");
        }

        if (!RetentionPolicy::isValid($this->retention_policy)) {
            throw new \InvalidArgumentException(
                "DataExtension '{$this->name}': unknown retention_policy '{$this->retention_policy}'."
            );
        }

        if ($this->retention_policy === RetentionPolicy::EXTENSION_DATE) {
            if (empty($this->delete_date)) {
                throw new \InvalidArgumentException(
                    "DataExtension '{$this->name}': delete_date is required for retention_policy EXTENSION_DATE."
                );
            }
        }

        $durationPolicies = [
            RetentionPolicy::EXTENSION_DURATION,
            RetentionPolicy::EXTENSION_DURATION_RENEW_ON_MODIFICATION,
            RetentionPolicy::RECORDS_DURATION,
        ];

        if (in_array($this->retention_policy, $durationPolicies, true)) {
            if ($this->delete_interval === null) {
                throw new \InvalidArgumentException(
                    "DataExtension '{$this->name}': delete_interval is required for retention_policy {$this->retention_policy}."
                );
            }
            if (empty($this->delete_interval_unit)) {
                throw new \InvalidArgumentException(
                    "DataExtension '{$this->name}': delete_interval_unit is required for retention_policy {$this->retention_policy}."
                );
            }
            if (!DeleteIntervalUnit::isValid($this->delete_interval_unit)) {
                throw new \InvalidArgumentException(
                    "DataExtension '{$this->name}': unknown delete_interval_unit '{$this->delete_interval_unit}'."
                );
            }
        }

        foreach ($this->fields as $field) {
            $field->validate();
        }
    }
}
