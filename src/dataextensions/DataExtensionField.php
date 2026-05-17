<?php

declare(strict_types=1);

namespace de\xqueue\maileon\api\client\dataextensions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * Represents a single field definition from a data extension.
 *
 * Field definitions are embedded in the full extension object returned by:
 *   GET /dataextensions/{id}
 *
 * The Maileon API uses SnakeCaseStrategy (Jackson), so boolean property
 * names like uniqueIdentifier arrive as unique_identifier in JSON.
 * Property names here mirror those snake_case keys exactly.
 *
 * Confirmed field list from ApiField.java (Maileon Eagle API 2.0):
 * {
 *   "id": 7,
 *   "name": "email",
 *   "description": "Primary contact email",
 *   "nullable": false,
 *   "unique_identifier": true,
 *   "data_type": "STRING",
 *   "default_value": null
 * }
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class DataExtensionField extends AbstractJSONWrapper
{
    /**
     * Numeric field ID within Maileon.
     *
     * @var int|null
     */
    public ?int $id = null;

    /**
     * The field name used as the key in record objects.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * Optional human-readable description of what this field stores.
     *
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Whether this field may store NULL values.
     * When false and no default_value is set, the field is mandatory on insert.
     *
     * @var bool|null
     */
    public ?bool $nullable = null;

    /**
     * Whether this field is a unique identifier for records in this extension.
     * Unique-identifier fields are used as the natural key for upsert operations.
     *
     * @var bool|null
     */
    public ?bool $unique_identifier = null;

    /**
     * The Maileon field data type constant.
     * Known values include: STRING, INTEGER, FLOAT, BOOLEAN, DATE, TIMESTAMP.
     *
     * @var string|null
     */
    public ?string $data_type = null;

    /**
     * Default value applied when a record is inserted without an explicit value.
     * NULL means no default — combined with nullable=false this makes the field required.
     *
     * @var string|null
     */
    public ?string $default_value = null;

    /**
     * Determine whether a value for this field is required when inserting a record.
     *
     * A field is required if:
     *   - it is a unique identifier (acts as the record's natural key), OR
     *   - it does not allow nulls AND has no server-side default value.
     *
     * @return bool True when a value must be supplied by the caller.
     */
    public function isRequired(): bool
    {
        if ($this->unique_identifier === true) {
            return true;
        }

        return $this->nullable === false && $this->default_value === null;
    }
}
