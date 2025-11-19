<?php

namespace de\xqueue\maileon\api\client\mailings;

/**
 * A type descriptor class for attribute definitions.
 *
 * The supported RSS orders are pubdate, title and link.
 *
 * @author Andreas Lange
 */
class DispatchLogicRSSOrderBy
{
    public static $PUBDATE;
    public static $TITLE;
    public static $LINK;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "type descriptor"
    /**
     * A string that describes the RSS order. Valid values are "pubdate", "title" and "link".
     *
     * @var string
     */
    public $value;

    /**
     * Creates a new DispatchLogicRSSOrderBy object.
     *
     * @param string $value a string describing the RSS order. Valid values are "pubdate", "title" and "link".
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public static function init()
    {
        if (self::$initialized === false) {
            self::$PUBDATE     = new DispatchLogicRSSOrderBy('pubdate');
            self::$TITLE       = new DispatchLogicRSSOrderBy('title');
            self::$LINK        = new DispatchLogicRSSOrderBy('link');
            self::$initialized = true;
        }
    }

    /**
     * Get the RSS order object by type descriptor.
     *
     * @param string $value a type descriptor string. Can be "pubdate", "title" or "link".
     *
     * @return DispatchLogicRSSOrderBy|null the DispatchLogicRSSOrderBy object
     */
    public static function getObject($value)
    {
        switch ($value) {
            case 'pubdate':
                return self::$PUBDATE;
            case 'title':
                return self::$TITLE;
            case 'link':
                return self::$LINK;
            default:
                return null;
        }
    }

    /**
     * @return string the type descriptor string of this DispatchLogicRSSOrderBy. Can be "pubdate", "title" or "link".
     */
    public function getValue(): string
    {
        return $this->value;
    }
}

DispatchLogicRSSOrderBy::init();
