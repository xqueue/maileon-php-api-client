<?php

namespace Maileon\Contacts;

/**
 * The wrapper class for a Maileon permission. 1 = NONE, 2 = SOI, 3 = COI, 4 = DOI, 5 = DOI+, 6 = OTHER.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Permission
{

    public static $NONE;
    public static $SOI;
    public static $COI;
    public static $DOI;
    public static $DOI_PLUS;
    public static $OTHER;
    private static $initialized = false;

    public $code;
    public $type;

    /**
     * This is the initialization method for the permission types. This must be called once in the beginning.
     */
    public static function init()
    {
        if (self::$initialized == false) {
            self::$NONE = new Permission(1, "none");
            self::$SOI = new Permission(2, "soi");
            self::$COI = new Permission(3, "coi");
            self::$DOI = new Permission(4, "doi");
            self::$DOI_PLUS = new Permission(5, "doi+");
            self::$OTHER = new Permission(6, "other");
            self::$initialized = true;
        }
    }

    /**
     * Constructor initializing the code of the permission.
     *
     * @param number $code
     *  The code to use for the constructed permission.
     */
    public function __construct($code = 0, $type = null)
    {
        $this->code = $code < 1 || $code > 6 ? 6 : $code;
        if ($type === null) {
            $this->type = $this->getType($code);
        } else {
            $this->type = $type;
        }
    }

    /**
     * Get the code of this permission.
     * 1 = NONE, 2 = SOI, 3 = COI, 4 = DOI, 5 = DOI+, 6 = OTHER.
     *
     * @return \em number
     *  the code of the permission object
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the type string of this permission.
     * none = NONE, soi = SOI, coi = COI, doi = DOI, doi+ = DOI+, other = OTHER.
     *
     * @return \em string
     *  the type of the permission object
     */
    public function getString()
    {
        return $this->type;
    }

    private function getType($code)
    {
        switch ($code) {
            case 1:
                return 'none';
            case 2:
                return 'soi';
            case 3:
                return 'coi';
            case 4:
                return 'doi';
            case 5:
                return 'doi+';
            case 6:
                return 'other';
            default:
                return 'other';
        }
    }
    
    /**
     * Get the permission object from the code
     *
     * @param var $code
     *  The code or type to get the permission object for.
     * @return \em Permission
     *  The permission object for the given code.
     */
    public static function getPermission($code)
    {
        switch ($code) {
            case 1:
                return self::$NONE;
            case "none":
                return self::$NONE;
            case 2:
                return self::$SOI;
            case "soi":
                return self::$SOI;
            case 3:
                return self::$COI;
            case "coi":
                return self::$COI;
            case 4:
                return self::$DOI;
            case "doi":
                return self::$DOI;
            case 5:
                return self::$DOI_PLUS;
            case "doi+":
                return self::$DOI_PLUS;
            case 6:
                return self::$OTHER;
            case "other":
                return self::$OTHER;

            default:
                return self::$OTHER;
        }
    }
}
Permission::init();
