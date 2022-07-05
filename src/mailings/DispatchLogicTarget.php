<?php

namespace de\xqueue\maileon\api\client\mailings;

/**
 * A type descriptor class for attribute definitions.
 *
 * The supported targets are event, contactfilter and rss.
 *
 * @author Andreas Lange | XQueue GmbH |  <a href="andreas.lange@xqueue.com">andreas.lange@xqueue.com</a>
 */
class DispatchLogicTarget
{
    public static $EVENT;
    public static $CONTACTFILTER;
    public static $RSS;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "type descriptor"
    /**
     *
     * @var string $value
     * A string that describes the target. Valid values are "event", "contactfilter" and "rss".
     */
    public $value;

    public static function init()
    {
        if (self::$initialized == false) {
            self::$EVENT = new DispatchLogicTarget("event");
            self::$CONTACTFILTER = new DispatchLogicTarget("contactfilter");
            self::$RSS = new DispatchLogicTarget("rss");
            self::$initialized = true;
        }
    }

    /**
     * Creates a new DispatchLogicTarget object.
     *
     * @param string $value
     * a string describing the target. Valid values are "event", "contactfilter" and "rss".
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     * the type descriptor string of this DispatchLogicTarget. Can be "event", "contactfilter" or "rss".
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the target object by type descriptor.
     *
     * @param string $value
     * a type descriptor string. Can be "event", "contactfilter" or "rss".
     * @return DispatchLogicTarget
     * the DispatchLogicTarget object
     */
    public static function getObject($value)
    {
        switch ($value) {
            case "event":
                return self::$EVENT;
            case "contactfilter":
                return self::$CONTACTFILTER;
            case "rss":
                return self::$RSS;
    
            default:
                return null;
        }
    }
}
DispatchLogicTarget::init();
