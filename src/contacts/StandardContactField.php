<?php

namespace de\xqueue\maileon\api\client\contacts;

/**
 * The class contains the valid names for a Maileon standard contact field
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class StandardContactField
{
    /** The Constant ADDRESS. */
    public static $ADDRESS = "ADDRESS";

    /** The Constant BIRTHDAY. */
    public static $BIRTHDAY = "BIRTHDAY";

    /** The Constant CITY. */
    public static $CITY = "CITY";

    /** The Constant COUNTRY. */
    public static $COUNTRY = "COUNTRY";

    /** The Constant FIRSTNAME. */
    public static $FIRSTNAME = "FIRSTNAME";

    /** The Constant FULLNAME. */
    public static $FULLNAME = "FULLNAME";

    /** The Constant GENDER. */
    public static $GENDER = "GENDER";

    /** The Constant HNR. */
    public static $HNR = "HNR";

    /** The Constant LASTNAME. */
    public static $LASTNAME = "LASTNAME";

    /** The Constant LOCALE. */
    public static $LOCALE = "LOCALE";

    /** The Constant NAMEDAY. */
    public static $NAMEDAY = "NAMEDAY";

    /** The Constant ORGANIZATION. */
    public static $ORGANIZATION = "ORGANIZATION";

    /** The Constant REGION. */
    public static $REGION = "REGION";

    /** The Constant SALUTATION. */
    public static $SALUTATION = "SALUTATION";

    /** The Constant TITLE. */
    public static $TITLE = "TITLE";

    /** The Constant ZIP. */
    public static $ZIP = "ZIP";
    
    /** The Constant STATE. */
    public static $STATE = "STATE";
    
    /** The Constant SENDOUT_STATUS. Sendout status can be "blocked" or "allowed" */
    public static $SENDOUT_STATUS = "SENDOUT_STATUS";
    
    /** The Constant PERMISSION_STATUS. Permission status can be "available" (permission != none and not unsubscribed), "none" (no permission given, yet), or "unsubscribed" */
    public static $PERMISSION_STATUS = "PERMISSION_STATUS";
    
	/** The Constant CUSTOM_SOURCE. The name of the source, as specified during creation of the contact (parameter src). */
    public static $CUSTOM_SOURCE = "CUSTOM_SOURCE";
    
    /** The Constant CHECKSUM. The checksum of the contact, used to retrieve or update contact information in insecure envorinments, e.g. publicly available landingpages (e.g. profile update pages) that use email, ID or external ID to identify a contact. As a second parameter, you can use the checksum to avoid users passing guessed identifyers. */
    public static $CHECKSUM = "CHECKSUM";


    public static function init()
    {
        // Nothing to initialize
    }
}
StandardContactField::init();
