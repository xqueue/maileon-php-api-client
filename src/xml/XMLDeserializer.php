<?php

namespace de\xqueue\maileon\api\client\xml;

use de\xqueue\maileon\api\client\account\AccountPlaceholder;
use de\xqueue\maileon\api\client\account\MailingDomain;
use de\xqueue\maileon\api\client\blacklists\Blacklist;
use de\xqueue\maileon\api\client\blacklists\mailings\FilteredMailingBlacklistExpression;
use de\xqueue\maileon\api\client\blacklists\mailings\MailingBlacklistExpressions;
use de\xqueue\maileon\api\client\contactfilters\ContactFilter;
use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\Contacts;
use de\xqueue\maileon\api\client\contacts\CustomFields;
use de\xqueue\maileon\api\client\contacts\Preference;
use de\xqueue\maileon\api\client\contacts\PreferenceCategory;
use de\xqueue\maileon\api\client\mailings\Attachment;
use de\xqueue\maileon\api\client\mailings\CustomProperty;
use de\xqueue\maileon\api\client\mailings\Mailing;
use de\xqueue\maileon\api\client\mailings\MailingBlacklist;
use de\xqueue\maileon\api\client\mailings\Schedule;
use de\xqueue\maileon\api\client\reports\Block;
use de\xqueue\maileon\api\client\reports\Bounce;
use de\xqueue\maileon\api\client\reports\Click;
use de\xqueue\maileon\api\client\reports\Conversion;
use de\xqueue\maileon\api\client\reports\Open;
use de\xqueue\maileon\api\client\reports\Recipient;
use de\xqueue\maileon\api\client\reports\Subscriber;
use de\xqueue\maileon\api\client\reports\UniqueBounce;
use de\xqueue\maileon\api\client\reports\UniqueConversion;
use de\xqueue\maileon\api\client\reports\Unsubscriber;
use de\xqueue\maileon\api\client\reports\UnsubscriptionReason;
use de\xqueue\maileon\api\client\targetgroups\TargetGroup;
use de\xqueue\maileon\api\client\transactions\TransactionType;

use function explode;
use function strtolower;

class XMLDeserializer
{
    public static function deserialize($xmlElement)
    {
        if (isset($xmlElement)) {
            $result = null;

            switch (strtolower($xmlElement->getName())) {
                // int
                case 'count':
                case 'id':
                case 'targetgroupid':
                case 'count_attachments':
                case 'transaction_type_id':
                case 'count_filters':
                    return (int) $xmlElement;
                // __toString() caused error (not found) on several servers
                // return (int) $xmlElement->__toString();

                // as is
                case 'name':
                case 'locale':
                case 'doi_key':
                    return $xmlElement; // (string) $xmlElement
                case 'tags':
                    return explode('#', $xmlElement); // explode('#', (string) $xmlElement)
                case 'templateId':
                case 'templateid':
                case 'previewtext':
                case 'subject':
                case 'sender':
                case 'senderalias':
                case 'ignore_permission':
                case 'state':
                case 'url':
                case 'type':
                case 'domain':
                case 'tracking_strategy':
                case 'speed_level':
                case 'template_path':
                case 'recipientalias':
                    return (string) $xmlElement;
                case 'events':
                case 'event':
                    return false; // deserialization not yet supported.
                case 'ignorepermission':
                case 'cleanup':
                    return (bool) $xmlElement;
                case 'result':
                    $result = [];

                    if (! empty($xmlElement->contact_filter_id)) {
                        $result['contact_filter_id'] = $xmlElement->contact_filter_id;
                    }

                    if (! empty($xmlElement->target_group_id) && ($xmlElement->target_group_id != -1)) {
                        $result['target_group_id'] = $xmlElement->target_group_id;
                    }

                    return $result;
                case 'schedule':
                    $result = new Schedule();

                    break;
                case 'targetgroup':
                    $result = new TargetGroup();

                    break;
                case 'mailing_blacklists':
                case 'filtered_expressions':
                case 'mailing_domains':
                case 'account_placeholders':
                case 'properties':
                case 'unsubscription_reasons':
                case 'unsubscriptions':
                case 'blacklists':
                case 'mailings':
                case 'blocks':
                case 'bounces':
                case 'unique_bounces':
                case 'clicks':
                case 'opens':
                case 'recipients':
                case 'transaction_types':
                case 'field_backups':
                case 'subscribers':
                case 'attachments':
                case 'unique_conversions':
                case 'conversions':
                case 'preference_categories':
                case 'preferences':
                case 'contactfilters':
                case 'targetgroups':
                    $result = [];

                    foreach ($xmlElement as $element) {
                        $result[] = self::deserialize($element);
                    }

                    return $result;
                case 'mailing_blacklist':
                    $result = new MailingBlacklist();

                    break;
                case 'filtered_expression':
                    $result = new FilteredMailingBlacklistExpression();

                    break;
                case 'mailing_blacklist_expressions':
                    $result = new MailingBlacklistExpressions();

                    break;
                case 'contactfilter':
                    $result = new ContactFilter();

                    break;
                case 'preference':
                    $result = new Preference();

                    break;
                case 'preference_category':
                    $result = new PreferenceCategory();

                    break;
                case 'conversion':
                    $result = new Conversion();

                    break;
                case 'unique_conversion':
                    $result = new UniqueConversion();

                    break;
                case 'contact':
                    $result = new Contact();

                    break;
                case 'contacts':
                    $result = new Contacts();

                    break;
                case 'attachment':
                    $result = new Attachment();

                    break;
                case 'custom_fields':
                    $result = new CustomFields();

                    break;
                case 'unsubscription':
                    $result = new Unsubscriber();

                    break;
                case 'unsubscription_reason':
                    $result = new UnsubscriptionReason();

                    break;
                case 'subscriber':
                    $result = new Subscriber();

                    break;
                case 'transaction_type':
                    $result = new TransactionType();

                    break;
                case 'recipient':
                    $result = new Recipient();

                    break;
                case 'open':
                    $result = new Open();

                    break;
                case 'click':
                    $result = new Click();

                    break;
                case 'bounce':
                    $result = new Bounce();

                    break;
                case 'unique_bounce':
                    $result = new UniqueBounce();

                    break;
                case 'block':
                    $result = new Block();

                    break;
                case 'mailing':
                    $result = new Mailing();

                    break;
                case 'blacklist':
                    $result = new Blacklist();

                    break;
                case 'property':
                    $result = new CustomProperty();

                    break;
                case 'account_placeholder':
                    $result = new AccountPlaceholder();

                    break;
                case 'mailing_domain':
                    $result = new MailingDomain();

                    break;
                case 'field_backup':
                case 'mailing_blacklist_expression':
                default:
                    break;
            }

            if (null !== $result) {
                $result->fromXML($xmlElement);

                return $result;
            }
        }

        return false;
    }
}
