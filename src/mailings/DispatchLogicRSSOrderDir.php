<?php

namespace de\xqueue\maileon\api\client\mailings;

/**
 * A type descriptor class for attribute definitions.
 *
 * The supported RSS order directions are asc and desc.
 *
 * @author Andreas Lange | XQueue GmbH |  <a href="andreas.lange@xqueue.com">andreas.lange@xqueue.com</a>
 */
class DispatchLogicRSSOrderDir
{
    public static $ASC;
    public static $DESC;

    private static $initialized = false;

    // TODO use a more sensible name for this concept, e.g. "type descriptor"
    /**
     *
     * @var string $value
     * A string that describes the RSS order. Valid values are "asc" and "desc".
     */
    public $value;

    public static function init()
    {
        if (self::$initialized == false) {
            self::$ASC = new DispatchLogicRSSOrderDir("asc");
            self::$DESC = new DispatchLogicRSSOrderDir("desc");
            self::$initialized = true;
        }
    }

    /**
     * Creates a new DispatchLogicRSSOrderDir object.
     *
     * @param string $value
     * a string describing the RSS order. Valid values are "asc" and "desc".
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     * the type descriptor string of this DispatchLogicRSSOrderDir. Can be "asc" or "desc".
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the RSS order object by type descriptor.
     *
     * @param string $value
     * a type descriptor string. Can be "asc" or "desc".
     * @return DispatchLogicRSSOrderDir
     * the DispatchLogicRSSOrderDir object
     */
    public static function getObject($value)
    {
        switch ($value) {
            case "asc":
                return self::$ASC;
            case "desc":
                return self::$DESC;

            default:
                return null;
        }
    }
}
DispatchLogicRSSOrderDir::init();
