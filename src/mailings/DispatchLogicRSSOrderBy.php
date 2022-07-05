<?php

namespace de\xqueue\maileon\api\client\mailings;

/**
 * A type descriptor class for attribute definitions.
 *
 * The supported RSS orders are pubdate, title and link.
 *
 * @author Andreas Lange | XQueue GmbH |  <a href="andreas.lange@xqueue.com">andreas.lange@xqueue.com</a>
 */
class DispatchLogicRSSOrderBy
{
    public static $PUBDATE;
    public static $TITLE;
    public static $LINK;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "type descriptor"
    /**
     *
     * @var string $value
     * A string that describes the RSS order. Valid values are "pubdate", "title" and "link".
     */
    public $value;

    public static function init()
    {
        if (self::$initialized == false) {
            self::$PUBDATE = new DispatchLogicRSSOrderBy("pubdate");
            self::$TITLE = new DispatchLogicRSSOrderBy("title");
            self::$LINK = new DispatchLogicRSSOrderBy("link");
            self::$initialized = true;
        }
    }

    /**
     * Creates a new DispatchLogicRSSOrderBy object.
     *
     * @param string $value
     * a string describing the RSS order. Valid values are "pubdate", "title" and "link".
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     * the type descriptor string of this DispatchLogicRSSOrderBy. Can be "pubdate", "title" or "link".
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the RSS order object by type descriptor.
     *
     * @param string $value
     * a type descriptor string. Can be "pubdate", "title" or "link".
     * @return DispatchLogicRSSOrderBy
     * the DispatchLogicRSSOrderBy object
     */
    public static function getObject($value)
    {
        switch ($value) {
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
DispatchLogicRSSOrderBy::init();
