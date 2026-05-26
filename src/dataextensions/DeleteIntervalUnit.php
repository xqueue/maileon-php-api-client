<?php

declare(strict_types=1);

namespace de\xqueue\maileon\api\client\dataextensions;

/**
 * Valid values for the delete_interval_unit field on a data extension.
 *
 * Used together with delete_interval when retention_policy is one of:
 * EXTENSION_DURATION, EXTENSION_DURATION_RENEW_ON_MODIFICATION, RECORDS_DURATION.
 *
 * @see RetentionPolicy
 * @see DataExtension::$delete_interval_unit
 */
class DeleteIntervalUnit
{
    public const DAYS   = 'DAYS';
    public const WEEKS  = 'WEEKS';
    public const MONTHS = 'MONTHS';

    public const ALL = [self::DAYS, self::WEEKS, self::MONTHS];

    public static function isValid(string $value): bool
    {
        return in_array($value, self::ALL, true);
    }

    private function __construct() {}
}
