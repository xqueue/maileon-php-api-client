<?php

namespace de\xqueue\maileon\api\client\account;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use Exception;
use SimpleXMLElement;

use function dom_import_simplexml;
use function is_array;

/**
 * Facade that wraps the REST service for accounts.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class AccountService extends AbstractMaileonService
{
    /**
     * Get account information
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getAccountInfo()
    {
        return $this->get(
            'account/info',
            [],
            'application/json'
        );
    }

    /**
     * Get list of all account placeholders.
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getAccountPlaceholders()
    {
        return $this->get('account/placeholders');
    }

    /**
     * Sets the list of account placeholders. All current account placeholders will be overwritten or removed,
     * if not contained in the new array
     *
     * @param array $accountPlaceholders Array of AccountPlaceholder or single account placeholder
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function setAccountPlaceholders($accountPlaceholders)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><account_placeholders></account_placeholders>');

        if (is_array($accountPlaceholders)) {
            foreach ($accountPlaceholders as $accountPlaceholder) {
                $this->sxmlAppend($xml, $accountPlaceholder->toXML());
            }
        } else {
            $this->sxmlAppend($xml, $accountPlaceholders->toXML());
        }

        return $this->post(
            'account/placeholders',
            $xml->asXML()
        );
    }

    /**
     * Update account placeholders. If account placeholder is not existing yet, it will be added.
     * If account placeholder with given name is available the value will be updated.
     * Other existing account placeholders will not be touched.
     *
     * @param array $accountPlaceholders Array of AccountPlaceholder or single account placeholder
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function updateAccountPlaceholders($accountPlaceholders)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><account_placeholders></account_placeholders>');

        if (is_array($accountPlaceholders)) {
            foreach ($accountPlaceholders as $accountPlaceholder) {
                $this->sxmlAppend($xml, $accountPlaceholder->toXML());
            }
        } else {
            $this->sxmlAppend($xml, $accountPlaceholders->toXML());
        }

        return $this->put(
            'account/placeholders',
            $xml->asXML()
        );
    }

    /**
     * Delete account placeholder with requested {@code name}
     *
     * @param string $name The name of the property to delete
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function deleteAccountPlaceholder($name)
    {
        $queryParameters = ['name' => $name];

        return $this->delete(
            'account/placeholders',
            $queryParameters
        );
    }

    /**
     * Get list of all subdomains
     *
     * @return MaileonAPIResult|null The result object of the API call, internal result object available at MaileonAPIResult::getResult()
     *
     * @throws MaileonAPIException|Exception If there was a connection problem or a server error occurred
     */
    public function getAccountMailingDomains()
    {
        return $this->get(
            'account/mailing_domains',
            [],
            'application/xml'
        );
    }

    /**
     * Append a SimpleXMLElement to another
     *
     * @param SimpleXMLElement $to
     * @param SimpleXMLElement $from
     *
     * @return void
     */
    public function sxmlAppend(
        SimpleXMLElement $to,
        SimpleXMLElement $from
    ) {
        $toDom   = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);

        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
}
