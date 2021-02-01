<?php

namespace de\xqueue\maileon\api\client\xml;

use de\xqueue\maileon\api\client\reports\Open;
use de\xqueue\maileon\api\client\reports\Block;
use de\xqueue\maileon\api\client\reports\Click;
use de\xqueue\maileon\api\client\reports\Bounce;
use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\mailings\Mailing;
use de\xqueue\maileon\api\client\contacts\Contacts;
use de\xqueue\maileon\api\client\mailings\Schedule;
use de\xqueue\maileon\api\client\reports\Recipient;
use de\xqueue\maileon\api\client\reports\Conversion;
use de\xqueue\maileon\api\client\reports\Subscriber;
use de\xqueue\maileon\api\client\mailings\Attachment;
use de\xqueue\maileon\api\client\mailings\MailingBlacklist;
use de\xqueue\maileon\api\client\reports\FieldBackup;
use de\xqueue\maileon\api\client\blacklists\Blacklist;
use de\xqueue\maileon\api\client\reports\UniqueBounce;
use de\xqueue\maileon\api\client\reports\Unsubscriber;
use de\xqueue\maileon\api\client\reports\UnsubscriptionReason;
use de\xqueue\maileon\api\client\contacts\CustomFields;
use de\xqueue\maileon\api\client\mailings\CustomProperty;
use de\xqueue\maileon\api\client\reports\UniqueConversion;
use de\xqueue\maileon\api\client\targetgroups\TargetGroup;
use de\xqueue\maileon\api\client\account\AccountPlaceholder;
use de\xqueue\maileon\api\client\contactfilters\ContactFilter;
use de\xqueue\maileon\api\client\transactions\TransactionType;
use de\xqueue\maileon\api\client\contactevents\ContactEventType;

class XMLDeserializer
{
    public static function deserialize($xmlElement)
    {
        if (isset($xmlElement)) {
            $result = null;
            switch (strtolower($xmlElement->getName())) {
                case "count":
                case "id":
                case "targetgroupid":
                case "count_attachments":
                    return (int)$xmlElement;
                    // __toString() caused error (not found) on several servers
                    // return (int)$xmlElement->__toString();
                case "doi_key":
                    return $xmlElement;
                case "tags":
                    return explode("#", $xmlElement);
                case "name":
                    return $xmlElement;
                case "templateId":
                case "previewtext":
                case "subject":
                case "senderalias":
                case "ignore_permission":
                case "state":
                case "url":
                case "type":
                    return (string)$xmlElement;
                case "locale":
                    return $xmlElement;
                case "event":
                    return false; // deserialization not yet supported.

                case "events":
                    return false; // deserialization not yet supported.
                    
                case "ignorepermission":
                case "cleanup":
                    return boolval($xmlElement);

                case "result":
                    $result = array();
                    if (!empty($xmlElement->contact_filter_id)) {
                        $result['contact_filter_id'] = $xmlElement->contact_filter_id;
                    }
                    if (!empty($xmlElement->target_group_id) && ($xmlElement->target_group_id!=-1)) {
                        $result['target_group_id'] = $xmlElement->target_group_id;
                    }
                    return $result;

                case "contacteventtype":
                    $result = new ContactEventType();
                    break;

                case "schedule":
                    $result = new Schedule();
                    break;

                case "contacteventtypes":
                    $result = array();
                    foreach ($xmlElement as $contactEventTypeElement) {
                        $result[] = self::deserialize($contactEventTypeElement);
                    }
                    return $result;
                    
                case "targetgroup":
                    $result = new TargetGroup();
                    break;
                    
                case "targetgroups":
                    $result = array();
                    foreach ($xmlElement as $element) {
                        $result[] = self::deserialize($element);
                    }
                    return $result;
                    
                case "mailing_blacklist":
                    $result = new MailingBlacklist();
                    break;
                    
                case "mailing_blacklists":
                    $result = array();
                    foreach ($xmlElement as $element) {
                        $result[] = self::deserialize($element);
                    }
                    return $result;

                case "contactfilter":
                    $result = new ContactFilter();
                    break;

                case "contactfilters":
                    $result = array();
                    foreach ($xmlElement as $contactFilterElement) {
                        $result[] = self::deserialize($contactFilterElement);
                    }
                    return $result;

                case "conversion":
                    $result = new Conversion();
                    break;

                case "conversions":
                    $result = array();
                    foreach ($xmlElement as $conversionElement) {
                        $result[] = self::deserialize($conversionElement);
                    }
                    return $result;

                case "unique_conversion":
                    $result = new UniqueConversion();
                    break;

                case "unique_conversions":
                    $result = array();
                    foreach ($xmlElement as $conversionElement) {
                        $result[] = self::deserialize($conversionElement);
                    }
                    return $result;

                case "contact":
                    $result = new Contact();
                    break;

                case "contacts":
                    $result = new Contacts();
                    break;
                
                case "attachment":
                    $result = new Attachment();
                    break;
                
                case "attachments":
                    $result = array();
                    foreach ($xmlElement as $attachmentElement) {
                        $result[] = self::deserialize($attachmentElement);
                    }
                    return $result;

                case "custom_fields":
                    $result = new CustomFields();
                    break;
                    
                case "unsubscription":
                    $result = new Unsubscriber();
                    break;
                    
                case "unsubscriptions":
                    $result = array();
                    foreach ($xmlElement as $unsubscriptionElement) {
                        $result[] = self::deserialize($unsubscriptionElement);
                    }
                    return $result;
                    
                case "unsubscription_reason":
                    $result = new UnsubscriptionReason();
                    break;
                    
                case "unsubscription_reasons":
                    $result = array();
                    foreach ($xmlElement as $element) {
                        $result[] = self::deserialize($element);
                    }
                    return $result;

                case "subscriber":
                    $result = new Subscriber();
                    break;

                case "subscribers":
                    $result = array();
                    foreach ($xmlElement as $subscriberElement) {
                        $result[] = self::deserialize($subscriberElement);
                    }
                    return $result;

                case "field_backup":
                    $result = new FieldBackup();
                    break;

                case "field_backups":
                    $result = array();
                    foreach ($xmlElement as $fieldBackupElement) {
                        $result[] = self::deserialize($fieldBackupElement);
                    }
                    return $result;

                case "transaction_type":
                    $result = new TransactionType();
                    break;

                case "transaction_types":
                    $result = array();
                    foreach ($xmlElement as $transactionTypeElement) {
                        $result[] = self::deserialize($transactionTypeElement);
                    }
                    return $result;

                case "transaction_type_id":
                    return (int)$xmlElement;

                case "recipient":
                    $result = new Recipient();
                    break;

                case "recipients":
                    $result = array();
                    foreach ($xmlElement as $recipientElement) {
                        $result[] = self::deserialize($recipientElement);
                    }
                    return $result;

                case "open":
                    $result = new Open();
                    break;

                case "opens":
                    $result = array();
                    foreach ($xmlElement as $openElement) {
                        $result[] = self::deserialize($openElement);
                    }
                    return $result;

                case "click":
                    $result = new Click();
                    break;

                case "clicks":
                    $result = array();
                    foreach ($xmlElement as $clickElement) {
                        $result[] = self::deserialize($clickElement);
                    }
                    return $result;

                case "bounce":
                    $result = new Bounce();
                    break;

                case "bounces":
                    $result = array();
                    foreach ($xmlElement as $bounceElement) {
                        $result[] = self::deserialize($bounceElement);
                    }
                    return $result;

                case "unique_bounce":
                    $result = new UniqueBounce();
                    break;

                case "unique_bounces":
                    $result = array();
                    foreach ($xmlElement as $bounceElement) {
                        $result[] = self::deserialize($bounceElement);
                    }
                    return $result;

                case "block":
                    $result = new Block();
                    break;

                case "blocks":
                    $result = array();
                    foreach ($xmlElement as $blockElement) {
                        $result[] = self::deserialize($blockElement);
                    }
                    return $result;

                case "mailing":
                    $result = new Mailing();
                    break;

                case "mailings":
                    $result = array();
                    foreach ($xmlElement as $mailingElement) {
                        $result[] = self::deserialize($mailingElement);
                    }
                    return $result;

                case "blacklist":
                    $result = new Blacklist();
                    break;

                case "blacklists":
                    $result = array();
                    foreach ($xmlElement as $blacklistElement) {
                        $result[] = self::deserialize($blacklistElement);
                    }
                    return $result;


                case "property":
                    $result = new CustomProperty();
                    break;

                case "properties":
                    $result = array();
                    foreach ($xmlElement as $element) {
                        $result[] = self::deserialize($element);
                    }
                    return $result;


                case "account_placeholder":
                    $result = new AccountPlaceholder();
                    break;

                case "account_placeholders":
                    $result = array();
                    foreach ($xmlElement as $element) {
                        $result[] = self::deserialize($element);
                    }
                    return $result;

                default:
                    $result = null;
                    break;
            }
            if ($result) {
                $result->fromXML($xmlElement);
                return $result;
            }
        }
        return false;
    }
}
