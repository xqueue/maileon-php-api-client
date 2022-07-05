<?php

namespace de\xqueue\maileon\api\client\mailings;

/**
 * A type descriptor class for attribute definitions.
 *
 * The supported speed levels are low, medium and high.
 *
 * @author Andreas Lange | XQueue GmbH |  <a href="andreas.lange@xqueue.com">andreas.lange@xqueue.com</a>
 */
class DispatchLogicSpeedLevel
{
    public static $LOW;
    public static $MEDIUM;
    public static $HIGH;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "type descriptor"
    /**
     *
     * @var string $value
     * A string that describes the speed level. Valid values are "low", "medium" and "high".
     */
    public $value;

    public static function init()
    {
        if (self::$initialized == false) {
            self::$LOW = new DispatchLogicInterval("low");
            self::$MEDIUM = new DispatchLogicInterval("medium");
            self::$HIGH = new DispatchLogicInterval("high");
            self::$initialized = true;
        }
    }

    /**
     * Creates a new DispatchLogicSpeedLevel object.
     *
     * @param string $value
     * a string describing the speed level. Valid values are "low", "medium" and "high".
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     * the type descriptor string of this DispatchLogicSpeedLevel. Can be "low", "medium" or "high".
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the speed level object by type descriptor.
     *
     * @param string $value
     * a type descriptor string. Can be "low", "medium" or "high".
     * @return DispatchLogicSpeedLevel
     * the DispatchLogicSpeedLevel object
     */
    public static function getObject($value)
    {
        switch ($value) {
            case "low":
                return self::$LOW;
            case "medium":
                return self::$MEDIUM;
            case "high":
                return self::$HIGH;

            default:
                return null;
        }
    }
}
DispatchLogicSpeedLevel::init();
