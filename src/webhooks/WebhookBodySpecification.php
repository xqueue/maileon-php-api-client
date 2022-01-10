<?php

namespace de\xqueue\maileon\api\client\webhooks;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A wrapper class for a webhook body specification
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class WebhookBodySpecification extends AbstractJSONWrapper
{
    public static $EVENT_FIELD_TIMESTAMP = 'timestamp';
    public static $EVENT_FIELD_MSG_ID = 'msg_id';
    public static $EVENT_FIELD_TRANSACTION_ID = 'transaction_id';
    public static $EVENT_FIELD_PROPERTY = 'property';
    public static $EVENT_FIELD_OLD_VALUE = 'old_value';
    public static $EVENT_FIELD_NEW_VALUE = 'new_value';

    /**
     * The custom fields for the webhook body
     *
     * @var string[]
     */
    public $customFields = [];

    /**
     * The standard fields for the webhook body
     *
     * @var string[]
     */
    public $standardFields = [];

    /**
     * The event fields for the webhook body
     *
     * @var string[]
     */
    public $eventFields = [];

    public function toArray()
    {
        $result = parent::toArray();

        $result['standardFields'] = array_map(function ($name) {
            return strtolower($name);
        }, $result['standardFields']);

        return $result;
    }
}
