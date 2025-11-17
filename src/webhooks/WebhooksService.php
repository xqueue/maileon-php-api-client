<?php

namespace de\xqueue\maileon\api\client\webhooks;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\json\JSONSerializer;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;

use function mb_convert_encoding;
use function rawurlencode;

/**
 * Facade that wraps the REST service for webhooks
 *
 */
class WebhooksService extends AbstractMaileonService
{

    /**
     * Retrieves the webhook with the given ID.
     *
     * @param int $id The ID of the webhook.
     *
     * @return MaileonAPIResult|null The result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getWebhook($id)
    {
        $encodedId = rawurlencode(mb_convert_encoding((string) $id, 'UTF-8'));

        return $this->get(
            "webhooks/$encodedId",
            [],
            'application/json',
            Webhook::class
        );
    }

    /**
     * Deletes the webhook with the given ID in the newsletter account.
     *
     * @param int $id The ID of the webhook.
     *
     * @return MaileonAPIResult|null The result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteWebhook($id)
    {
        $encodedId = rawurlencode(mb_convert_encoding((string) $id, 'UTF-8'));

        return $this->delete(
            "webhooks/$encodedId",
            [],
            'application/json'
        );
    }

    /**
     * Updates the webhook with the given ID in the newsletter account.
     *
     * @param int     $id      The ID of the webhook.
     * @param Webhook $webhook The updated webhook data.
     *
     * @return MaileonAPIResult|null The result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function updateWebhook(
        $id,
        $webhook
    ) {
        $encodedId = rawurlencode(mb_convert_encoding((string) $id, 'UTF-8'));

        return $this->put(
            "webhooks/$encodedId",
            JSONSerializer::json_encode($webhook),
            [],
            'application/json'
        );
    }

    /**
     * Creates a webhook in the newsletter account.
     *
     * @param Webhook $webhook The webhook data.
     *
     * @return MaileonAPIResult|null The result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function createWebhook($webhook)
    {
        return $this->post(
            'webhooks/',
            JSONSerializer::json_encode($webhook),
            [],
            'application/json'
        );
    }

    /**
     * Retrieves a list of webhooks configured in the newsletter account.
     *
     * @return MaileonAPIResult|null The result object of the API call, with a MailingBlacklist available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getWebhooks()
    {
        return $this->get(
            'webhooks/',
            [],
            'application/json',
            [
                'array',
                Webhook::class,
            ]
        );
    }
}

