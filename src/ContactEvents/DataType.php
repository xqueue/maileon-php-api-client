<?php

namespace Maileon\ContactEvents;

/**
 * A type descriptor class for attribute definitions.
 *
 * The supported data types are string, double, float, integer, boolean and timestamp.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class DataType
{
    public static $STRING;
    public static $DOUBLE;
    public static $FLOAT;
    public static $INTEGER;
    public static $BOOLEAN;
    public static $TIMESTAMP;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "type descriptor"
    /**
     *
     * @var string $value
     *  A string that describes the datatype. Valid values are "string", "double", "float",
     *  "integer", "boolean" and "timestamp".
     */
    public $value;

    public static function init()
    {
        if (self::$initialized == false) {
            self::$STRING = new DataType("string");
            self::$DOUBLE = new DataType("double");
            self::$FLOAT = new DataType("float");
            self::$INTEGER = new DataType("integer");
            self::$BOOLEAN = new DataType("boolean");
            self::$TIMESTAMP = new DataType("timestamp");
            self::$initialized = true;
        }
    }

    /**
     * Creates a new DataType object.
     *
     * @param string $value
     *  a string describing the data type. Valid values are "string", "double", "float",
     *  "integer", "boolean" and "timestamp".
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return \em string
     *  the type descriptor string of this DataType. Can be "string", "double", "float",
     *  "integer", "boolean" or "timestamp".
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the permission object by type descriptor.
     *
     * @param string $value
     *  a type descriptor string. Can be "string", "double", "float",
     *  "integer", "boolean" or "timestamp".
     * @return \em Permission
     *  the permission object
     */
    public static function getDataType($value)
    {
        switch ($value) {
            case "string":
                return self::$STRING;
            case "double":
                return self::$DOUBLE;
            case "float":
                return self::$FLOAT;
            case "integer":
                return self::$INTEGER;
            case "boolean":
                return self::$BOOLEAN;
            case "timestamp":
                return self::$TIMESTAMP;

            default:
                return null;
        }
    }
}
