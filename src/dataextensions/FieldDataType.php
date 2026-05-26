<?php

declare(strict_types=1);

namespace de\xqueue\maileon\api\client\dataextensions;

/**
 * Valid values for the data_type field on a DataExtensionField.
 *
 * All values are lowercase strings as returned by the Maileon API
 * (FieldDataType.getName() in the Java source).
 *
 * Usage:
 *   $field->data_type = FieldDataType::STRING;
 *
 * @see DataExtensionField::$data_type
 */
class FieldDataType
{
    public const STRING   = 'string';
    public const DOUBLE   = 'double';
    public const FLOAT    = 'float';
    public const INTEGER  = 'integer';
    public const BOOLEAN  = 'boolean';
    public const DATE     = 'date';
    public const TIMESTAMP = 'timestamp';

    /** Special field type that stores a contact email address. */
    public const CONTACT_EMAIL       = 'contact_email';
    /** Special field type that stores a contact external ID. */
    public const CONTACT_EXTERNAL_ID = 'contact_external_id';

    /** Fixed-length string variants (e.g. string10, string100, …). */
    public const STRING4    = 'string4';
    public const STRING5    = 'string5';
    public const STRING6    = 'string6';
    public const STRING7    = 'string7';
    public const STRING8    = 'string8';
    public const STRING9    = 'string9';
    public const STRING10   = 'string10';
    public const STRING20   = 'string20';
    public const STRING40   = 'string40';
    public const STRING50   = 'string50';
    public const STRING100  = 'string100';
    public const STRING120  = 'string120';
    public const STRING140  = 'string140';
    public const STRING160  = 'string160';
    public const STRING180  = 'string180';
    public const STRING200  = 'string200';
    public const STRING220  = 'string220';
    public const STRING240  = 'string240';
    public const STRING512  = 'string512';
    public const STRING600  = 'string600';
    public const STRING700  = 'string700';
    public const STRING800  = 'string800';
    public const STRING900  = 'string900';
    public const STRING1024 = 'string1024';
    public const STRING1100 = 'string1100';
    public const STRING1200 = 'string1200';
    public const STRING1300 = 'string1300';
    public const STRING1400 = 'string1400';
    public const STRING1500 = 'string1500';
    public const STRING1600 = 'string1600';
    public const STRING1700 = 'string1700';
    public const STRING1800 = 'string1800';
    public const STRING1900 = 'string1900';
    public const STRING2048 = 'string2048';
    public const STRING2100 = 'string2100';
    public const STRING4096 = 'string4096';

    private function __construct() {}
}
