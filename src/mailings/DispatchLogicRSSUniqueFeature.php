<?php

namespace de\xqueue\maileon\api\client\mailings;

/**
 * A type descriptor class for attribute definitions.
 *
 * The supported RSS unique features are default, pubdate, title and link.
 *
 * @author Andreas Lange | XQueue GmbH |  <a href="andreas.lange@xqueue.com">andreas.lange@xqueue.com</a>
 */
class DispatchLogicRSSUniqueFeature
{
    public static $DEFAULT;
    public static $PUBDATE;
    public static $TITLE;
    public static $LINK;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "type descriptor"
    /**
     *
     * @var string $value
     * A string that describes the RSS unique feature. Valid values are "default", "pubdate", "title" and "link".
     */
    public $value;

    public static function init()
    {
        if (self::$initialized == false) {
            self::$DEFAULT = new DispatchLogicRSSUniqueFeature("default");
            self::$PUBDATE = new DispatchLogicRSSUniqueFeature("pubdate");
            self::$TITLE = new DispatchLogicRSSUniqueFeature("title");
            self::$LINK = new DispatchLogicRSSUniqueFeature("link");
            self::$initialized = true;
        }
    }

    /**
     * Creates a new DispatchLogicRSSUniqueFeature object.
     *
     * @param string $value
     * a string describing the RSS unique feature. Valid values are "default", "pubdate", "title" and "link".
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     * the type descriptor string of this DispatchLogicRSSUniqueFeature. Can be "default", "pubdate", "title" or "link".
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the RSS unique feature object by type descriptor.
     *
     * @param string $value
     * a type descriptor string. Can be "default", "pubdate", "title" or "link".
     * @return DispatchLogicRSSUniqueFeature
     * the DispatchLogicRSSUniqueFeature object
     */
    public static function getObject($value)
    {
        switch ($value) {
            case "default":
                return self::$DEFAULT;
            case "pubdate":
                return self::$PUBDATE;
            case "title":
                return self::$TITLE;
            case "link":
                return self::$LINK;

            default:
                return null;
        }
    }
}
DispatchLogicRSSUniqueFeature::init();
