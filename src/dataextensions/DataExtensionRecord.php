<?php

declare(strict_types=1);

namespace de\xqueue\maileon\api\client\dataextensions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * Represents a single row returned by the data-extension records endpoint:
 *   GET /dataextensions/{id}/records?page_size=N&page_index=N
 *
 * The Maileon API returns each row as a JSON object whose keys are the field
 * names defined in the extension schema. Because the schema is determined at
 * runtime (different extensions have different fields), this class stores all
 * field values in a dynamic $values map rather than in typed properties.
 *
 * Example row JSON (for an extension with "email", "first_name", "score"):
 * {
 *   "email": "user@example.com",
 *   "first_name": "Jane",
 *   "score": 42
 * }
 *
 * Usage:
 *   $email = $record->get('email');             // "user@example.com"
 *   $fields = $record->getFieldNames();          // ["email", "first_name", "score"]
 *   $all = $record->getValues();                 // full associative array
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class DataExtensionRecord extends AbstractJSONWrapper
{
    /**
     * Dynamic map of field name => field value for this record row.
     * All values are stored as received from the API (string, int, float, bool, null).
     *
     * @var array<string, mixed>
     */
    public array $values = [];

    /**
     * Override fromArray to capture all dynamic field keys into the $values map
     * instead of assigning them to individual properties (which don't exist on
     * this class since the schema is unknown at compile time).
     *
     * @param \stdClass $object_vars Decoded JSON row object from json_decode().
     */
    public function fromArray($object_vars): void
    {
        // $object_vars is a stdClass; iterate its properties as key => value pairs.
        foreach ($object_vars as $key => $value) {
            $this->values[$key] = $value;
        }
    }

    /**
     * Override toArray to return the dynamic values map directly, omitting the
     * outer "values" key that parent::toArray() would wrap around it.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * Retrieve the value of a named field in this record.
     *
     * @param string $fieldName The field name as defined in the extension schema.
     *
     * @return mixed The field value, or null if the field is absent or explicitly null.
     */
    public function get(string $fieldName): mixed
    {
        return $this->values[$fieldName] ?? null;
    }

    /**
     * Return all field names present in this record.
     * Null-valued fields may be absent from the API response.
     *
     * @return string[]
     */
    public function getFieldNames(): array
    {
        return array_keys($this->values);
    }

    /**
     * Return the complete field-name => value map for this record.
     *
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Check whether a named field is present in this record.
     *
     * @param string $fieldName
     *
     * @return bool
     */
    public function has(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->values);
    }

    /**
     * Override to render field names alongside their values.
     * Base class implode(',', $values) drops associative keys.
     *
     * @return string e.g. DataExtensionRecord [ email=alice@example.com, score=10 ]
     */
    public function __toString(): string
    {
        $pairs = [];
        foreach ($this->values as $key => $value) {
            $pairs[] = $key . '=' . ($value ?? 'null');
        }
        return self::class . ' [ ' . implode(', ', $pairs) . ' ]';
    }
}
