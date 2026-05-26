<?php

declare(strict_types=1);

namespace de\xqueue\maileon\api\client\dataextensions;

/**
 * Valid values for the retention_policy field on a data extension.
 *
 * The retention policy controls how the extension or its records are
 * automatically removed over time. Deletion is not immediate — the system
 * guarantees data is kept until the limit is exceeded, but removal may
 * happen some time after that point.
 *
 * Usage:
 *   $extension->retention_policy = RetentionPolicy::NONE;
 *
 * @see DataExtension::$retention_policy
 */
class RetentionPolicy
{
    /** The extension and all its records are stored permanently. */
    public const NONE = 'NONE';

    /** The extension (and its records) is deleted when a specific date is reached. Requires delete_date. */
    public const EXTENSION_DATE = 'EXTENSION_DATE';

    /** The extension is deleted a fixed duration after creation. Requires delete_interval + delete_interval_unit. */
    public const EXTENSION_DURATION = 'EXTENSION_DURATION';

    /**
     * Like EXTENSION_DURATION but the countdown resets each time a record is
     * added, updated, or deleted. Requires delete_interval + delete_interval_unit.
     */
    public const EXTENSION_DURATION_RENEW_ON_MODIFICATION = 'EXTENSION_DURATION_RENEW_ON_MODIFICATION';

    /**
     * The extension itself is permanent but individual records are deleted a
     * fixed duration after their last modification. Requires delete_interval + delete_interval_unit.
     */
    public const RECORDS_DURATION = 'RECORDS_DURATION';

    /** All valid retention policy strings. */
    public const ALL = [
        self::NONE,
        self::EXTENSION_DATE,
        self::EXTENSION_DURATION,
        self::EXTENSION_DURATION_RENEW_ON_MODIFICATION,
        self::RECORDS_DURATION,
    ];

    public static function isValid(string $value): bool
    {
        return in_array($value, self::ALL, true);
    }

    private function __construct() {}
}
