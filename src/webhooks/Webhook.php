<?php

namespace de\xqueue\maileon\api\client\webhooks;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A wrapper class for a webhook
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class Webhook extends AbstractJSONWrapper
{
    public static $EVENT_UNSUBSCRIPTION = 'unsubscription';
    public static $EVENT_DOI = 'doi';
    public static $EVENT_BOUNCE = 'bounce';
    public static $EVENT_FILTERED = 'filtered';
    public static $EVENT_CONTACT_FIELD_CHANGE = 'contact_field_change';

    /**
     * The body specification of this webhook
     *
     * @var WebhookBodySpecification
     */
    public $body = null;

    /**
     * The id of this webhook
     *
     * @var integer|null
     */
    public $id = null;

    /**
     * The newsletter account id of this webhook
     *
     * @var integer|null
     */
    public $newsletterAccountId = null;

    /**
     * The url of this webhook
     *
     * @var string
     */
    public $url = '';

    /**
     * The event of this webhook
     *
     * @var string
     */
    public $event = '';

    /**
     * The url parameters of this webhook
     *
     * @var WebhookUrlParameter[]
     */
    public $urlParams = [];

    public function fromArray($object_vars)
    {
        if (property_exists($object_vars, 'urlParams') && $object_vars->urlParams !== null && is_array($object_vars->urlParams)) {
            foreach ($object_vars->urlParams as $param) {
                $paramObject = new WebhookUrlParameter();
                $paramObject->fromArray($param);

                $this->urlParams []= $paramObject;
            }

            unset($object_vars->urlParams);
        }

        $this->body = new WebhookBodySpecification();
        if (property_exists($object_vars, 'bodySpec') && $object_vars->bodySpec !== null) {
            $this->body->fromArray($object_vars->bodySpec);

            unset($object_vars->bodySpec);
        }

        foreach(get_object_vars($this->body) as $key => $value) {
            if(property_exists($object_vars, $key)) {
                $this->body->{$key} = $object_vars->{$key};
                unset($object_vars->{$key});
            }
        }

        if(property_exists($object_vars, 'nlAccountId')) {
            $this->newsletterAccountId = $object_vars->nlAccountId;
            unset($object_vars->nlAccountId);
        }

        parent::fromArray($object_vars);
    }

    public function toArray()
    {
        $result = parent::toArray();
        unset($result['body']);

        return array_merge($result, $this->body->toArray());
    }
}
