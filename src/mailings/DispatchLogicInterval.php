<?php

namespace de\xqueue\maileon\api\client\mailings;

/**
 * A type descriptor class for attribute definitions.
 *
 * The supported intervals are hour, day, week and month.
 *
 * @author Andreas Lange | XQueue GmbH |  <a href="andreas.lange@xqueue.com">andreas.lange@xqueue.com</a>
 */
class DispatchLogicInterval
{
    public static $HOUR;
    public static $DAY;
    public static $WEEK;
    public static $MONTH;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "type descriptor"
    /**
     *
     * @var string $value
     * A string that describes the interval. Valid values are "hour", "day", "week" and "month".
     */
    public $value;

    public static function init()
    {
        if (self::$initialized == false) {
            self::$HOUR = new DispatchLogicInterval("hour");
            self::$DAY = new DispatchLogicInterval("day");
            self::$WEEK = new DispatchLogicInterval("week");
            self::$MONTH = new DispatchLogicInterval("month");
            self::$initialized = true;
        }
    }

    /**
     * Creates a new DispatchLogicInterval object.
     *
     * @param string $value
     * a string describing the logic interval. Valid values are "hour", "day", "week" and "month".
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     * the type descriptor string of this DispatchLogicInterval. Can be "hour", "day", "week" or "month".
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the interval object by type descriptor.
     *
     * @param string $value
     * a type descriptor string. Can be "hour", "day", "week" or "month".
     * @return DispatchLogicInterval
     * the DispatchLogicInterval object
     */
    public static function getObject($value)
    {
        switch ($value) {
            case "hour":
                return self::$HOUR;
            case "day":
                return self::$DAY;
            case "week":
                return self::$WEEK;
            case "month":
                return self::$MONTH;

            default:
                return null;
        }
    }
}
DispatchLogicInterval::init();
