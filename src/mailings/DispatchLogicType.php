<?php

namespace de\xqueue\maileon\api\client\mailings;

/**
 * A DispatchLogicType descriptor class for attribute definitions.
 *
 * The supported DispatchLogicTypes are single and multi.
 *
 * @author Andreas Lange
 */
class DispatchLogicType
{
    public static $SINGLE;
    public static $MULTI;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "DispatchLogicType descriptor"
    /**
     * A string that describes the DispatchLogicType. Valid values are "single" and "multi".
     *
     * @var string
     */
    public $value;

    public static function init()
    {
        if (self::$initialized === false) {
            self::$SINGLE      = new DispatchLogicType('single');
            self::$MULTI       = new DispatchLogicType('multi');
            self::$initialized = true;
        }
    }

    /**
     * Creates a new DispatchLogicType object.
     *
     * @param string $value a string describing the logic DispatchLogicType. Valid values are "single" and "multi".
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string the DispatchLogicType descriptor string of this DispatchLogicType. Can be "single" or "multi".
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get the logic DispatchLogicType object by DispatchLogicType descriptor.
     *
     * @param string $value a DispatchLogicType descriptor string. Can be "single" or "multi".
     *
     * @return DispatchLogicType|null the DispatchLogicType object
     */
    public static function getObject($value)
    {
        switch ($value) {
            case 'single':
                return self::$SINGLE;
            case 'multi':
                return self::$MULTI;
            default:
                return null;
        }
    }
}

DispatchLogicType::init();
