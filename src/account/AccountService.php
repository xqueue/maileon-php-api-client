<?php

namespace de\xqueue\maileon\api\client\account;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIResult;

/**
 * Facade that wraps the REST service for accounts.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class AccountService extends AbstractMaileonService
{
    /**
     * Get account informations
     *
     * @return MaileonAPIResult
     */
    public function getAccountInfo()
    {
        return $this->get(
            "account/info",
            [],
            "application/json"
        );
    }
    
    /**
     * Get list of all account placeholders.
     *
     * @return MaileonAPIResult
     */
    public function getAccountPlaceholders()
    {
        return $this->get("account/placeholders");
    }


    /**
     * Sets the list of account placeholders. All current account placeholders will be overwritten or removed,
     * if not contained in the new array
     *
     * @param array $accountPlaceholders Array of AccountPlaceholder or single account placeholder
     * @return MaileonAPIResult
     */
    public function setAccountPlaceholders($accountPlaceholders)
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><account_placeholders></account_placeholders>");

        if (is_array($accountPlaceholders)) {
            foreach ($accountPlaceholders as $accountPlaceholder) {
                $this->sxmlAppend($xml, $accountPlaceholder->toXML());
            }
        } else {
            $this->sxmlAppend($xml, $accountPlaceholders->toXML());
        }

        return $this->post("account/placeholders", $xml->asXML());
    }


    /**
     * Update account placeholders. If account placeholder is not existing yet, it will be added.
     * If account placeholder with given name is avaliable the value will be updated.
     * Other existing account placeholders will not be touched.
     *
     * @param array $accountPlaceholders Array of AccountPlaceholder or single account placeholder
     * @return MaileonAPIResult
     */
    public function updateAccountPlaceholders($accountPlaceholders)
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><account_placeholders></account_placeholders>");

        if (is_array($accountPlaceholders)) {
            foreach ($accountPlaceholders as $accountPlaceholder) {
                $this->sxmlAppend($xml, $accountPlaceholder->toXML());
            }
        } else {
            $this->sxmlAppend($xml, $accountPlaceholders->toXML());
        }

        return $this->put("account/placeholders", $xml->asXML());
    }


    /**
     * Delete account placeholder with requested {@code name}
     *
     * @param string $name The name of the property to delete
     * @return MaileonAPIResult
     */
    public function deleteAccountPlaceholder($name)
    {
        $queryParameters = array(
            'name' => $name,
        );

        return $this->delete("account/placeholders", $queryParameters);
    }


    public function sxmlAppend(\SimpleXMLElement $to, \SimpleXMLElement $from)
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
}
