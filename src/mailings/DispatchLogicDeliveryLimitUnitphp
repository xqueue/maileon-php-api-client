<?php

namespace de\xqueue\maileon\api\client\mailings;

/**
 * A type descriptor class for attribute definitions.
 *
 * The supported RSS order are day, week, month and year.
 *
 * @author Andreas Lange | XQueue GmbH |  <a href="andreas.lange@xqueue.com">andreas.lange@xqueue.com</a>
 */
class DispatchLogicDeliveryLimitUnit
{
    public static $DAY;
    public static $WEEK;
    public static $MONTH;
    public static $YEAR;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "type descriptor"
    /**
     *
     * @var string $value
     * A string that describes the RSS order. Valid values are day, week, month and year.
     */
    public $value;

    public static function init()
    {
        if (self::$initialized == false) {
            self::$DAY = new DispatchLogicDeliveryLimitUnit("day");
            self::$WEEK = new DispatchLogicDeliveryLimitUnit("week");
            self::$MONTH = new DispatchLogicDeliveryLimitUnit("month");
            self::$YEAR = new DispatchLogicDeliveryLimitUnit("year");
            self::$initialized = true;
        }
    }

    /**
     * Creates a new DispatchLogicDeliveryLimitUnit object.
     *
     * @param string $value
     * a string describing the RSS order. Valid values are day, week, month and year.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     * the type descriptor string of this DispatchLogicDeliveryLimitUnit. Can be day, week, month or year.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the RSS order object by type descriptor.
     *
     * @param string $value
     * a type descriptor string. Can be day, week, month or year.
     * @return DispatchLogicDeliveryLimitUnit
     * the DispatchLogicDeliveryLimitUnit object
     */
    public static function getObject($value)
    {
        switch ($value) {
            case "day":
                return self::$DAY;
            case "week":
                return self::$WEEK;
            case "month":
                return self::$MONTH;
            case "year":
                return self::$YEAR;
    
            default:
                return null;
        }
    }
}
DispatchLogicDeliveryLimitUnit::init();
