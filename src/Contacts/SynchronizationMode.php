<?php

namespace Maileon\Contacts;

/**
 * The wrapper class for a Maileon synchronization mode. 1 = UPDATE, 2 = IGNORE.
 *
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class SynchronizationMode
{

    public static $UPDATE;
    public static $IGNORE;
    private static $initialized = false;

    public $code;

    /**
     * This is the initialization method for the synchronization modes. This must be called once in the beginning.
     */
    public static function init()
    {
        if (self::$initialized == false) {
            self::$UPDATE = new SynchronizationMode(1);
            self::$IGNORE = new SynchronizationMode(2);
            self::$initialized = true;
        }
    }

    /**
     * Constructor initializing the code of the synchronization mode.
     *
     * @param number $code
     *  The code to use for the constructed synchronization mode.
     */
    public function __construct($code = 0)
    {
        $this->code = $code;
    }

    /**
     * Get the code of this synchronization mode. 1 = UPDATE, 2 = IGNORE.
     *
     * @return \em number
     *  the code of the synchronization mode object
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the synchronization mode from the code
     *
     * @param int $code
     *  the code to get the synchronization mode for
     * @return \em com_maileon_api_contacts_SynchronizationMode
     *  the synchronization mode for the given code
     */
    public static function getSynchronizationMode($code)
    {
        switch ($code) {
            case 1:
                return self::$UPDATE;
            case 2:
                return self::$IGNORE;

            default:
                return self::$IGNORE;
        }
    }
}
