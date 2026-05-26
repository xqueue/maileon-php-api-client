<?php

declare(strict_types=1);

namespace de\xqueue\maileon\api\client\dataextensions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * Represents a single entry from the paginated data-extension list endpoint:
 *   GET /dataextensions?page_size=N&page_index=N
 *
 * The Maileon API serialises field names with Jackson's SnakeCaseStrategy, so
 * every multi-word property (e.g. retentionPolicy) arrives as snake_case JSON
 * (retention_policy). The property names in this class therefore match the
 * snake_case JSON keys so that AbstractJSONWrapper::fromArray() can copy them
 * directly without any manual mapping.
 *
 * Example JSON element:
 * {
 *   "id": 42,
 *   "name": "Newsletter subscribers",
 *   "description": "Active opt-in contacts",
 *   "retention_policy": "NONE",
 *   "created_user": "admin@example.com",
 *   "created": "2024-01-15T08:00:00Z",
 *   "count_fields": 5,
 *   "count_records": 12000
 * }
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class DataExtensionSummary extends AbstractJSONWrapper
{
    /**
     * Numeric Maileon data extension ID.
     *
     * @var int|null
     */
    public ?int $id = null;

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
     * Retention policy. See RetentionPolicy for valid values.
     * NONE = stored permanently; other values trigger automatic deletion.
     *
     * @var string|null
     */
    public ?string $retention_policy = null;

    /**
     * Email address or login name of the user who created this extension.
     *
     * @var string|null
     */
    public ?string $created_user = null;

    /**
     * ISO 8601 timestamp of when this extension was created (e.g. "2024-01-15T08:00:00Z").
     *
     * @var string|null
     */
    public ?string $created = null;

    /**
     * Total number of field definitions in this extension.
     *
     * @var int|null
     */
    public ?int $count_fields = null;

    /**
     * Total number of data records currently stored in this extension.
     *
     * @var int|null
     */
    public ?int $count_records = null;
}
