<?php

namespace XQueue\Maileon\API\Mailings;

/**
 * The class contains the valid names for Maileon mailing fields to request from the API
 *
 * @author Marcus St&auml;nder | Trusted Technologies GmbH | <a href="mailto:marcus.staender@trusted-technologies.de">marcus.staender@trusted-technologies.de</a>
 *
 */
class MailingFields
{

    /** The Constant "type". */
    public static $TYPE = "type";

    /** The Constant "state". */
    public static $STATE = "state";

    /** The Constant "name". */
    public static $NAME = "name";

    /** The Constant "scheduleTime". */
    public static $SCHEDULE_TIME = "scheduleTime";


    static function init()
    {
        // Nothing to initialize
    }
}