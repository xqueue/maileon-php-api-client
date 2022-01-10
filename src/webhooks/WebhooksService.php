<?php

namespace de\xqueue\maileon\api\client\webhooks;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\json\JSONSerializer;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;

/**
 * Facade that wraps the REST service for webhooks
 *
 */
class WebhooksService extends AbstractMaileonService
{

    /**
     * Retrieves the webhook with the given ID.
     *
     * @param number $id
     *  The ID of the webhook.
     *
     * @return MaileonAPIResult
     * the result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function getWebhook($id)
    {
        return $this->get("webhooks/" . $id, [], 'application/json', Webhook::class);
    }

    /**
     * Deletes the webhook with the given ID in the newsletter account.
     *
     * @param number $id
     *  The ID of the webhook.
     *
     * @return MaileonAPIResult
     * the result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function deleteWebhook($id)
    {
        return $this->delete("webhooks/" . $id, [], 'application/json');
    }

    /**
     * Updates the webhook with the given ID in the newsletter account.
     *
     * @param number $id
     *  The ID of the webhook.
     * @param Webhook $webhook
     *  The updated webhook data.
     *
     * @return MaileonAPIResult
     * the result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function updateWebhook($id, $webhook)
    {
        return $this->put("webhooks/" . $id, JSONSerializer::json_encode($webhook), [], 'application/json');
    }

    /**
     * Creates a webhook in the newsletter account.
     *
     * @param Webhook $webhook
     *  The webhook data.
     *
     * @return MaileonAPIResult
     * the result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function createWebhook($webhook)
    {
        return $this->post("webhooks/", JSONSerializer::json_encode($webhook), [], 'application/json');
    }

    /**
     * Retrieves a list of webhooks configured in the newsletter account.
     *
     * @return MaileonAPIResult
     * the result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     * @throws MaileonAPIException
     * if there was a connection problem or a server error occurred
     */
    public function getWebhooks()
    {
        return $this->get("webhooks/", [], 'application/json', ['array', Webhook::class]);
    }
}

