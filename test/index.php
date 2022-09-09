<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <style type="text/css">
        input {
            margin-left: 30px;
        }

        li {
            list-style-type: none;
        }
    </style>
    <title>Maileon PHP API Client Testpage</title>
</head>
<body>
<img src="media/xqueue.jpg" alt="XQueue">

<form action="phpapitest.php" id="apiSelection" method="post">
    <h1>Maileon API Client Testpage</h1>


    <input type="checkbox" name="debug" value="true" checked="checked"> Enable debug mode


    <h2>Ping - Tests</h2>

    <p>
        <input type="checkbox" name="ping_1" value="true"> GET
        <input type="checkbox" name="ping_2" value="true"> PUT
        <input type="checkbox" name="ping_3" value="true"> POST
        <input type="checkbox" name="ping_4" value="true"> DELETE
    </p>

    <h2>Contact - Tests</h2>
    <ul>
        <li><input type="checkbox" name="contact_1" value="true"> POST Create contact</li>
        <li><input type="checkbox" name="contact_1_1" value="true"> POST Create contact by external ID</li>
        <li><input type="checkbox" name="contact_1_2" value="true"> PUT Update contact</li>
        <li><input type="checkbox" name="contact_2" value="true"> GET contacts count</li>
        <li><input type="checkbox" name="contact_2_1" value="true"> GET contacts count with update_after parameter</li>
        <li><input type="checkbox" name="contact_3" value="true"> GET all contacts [page and pagesize from config]</li>
        <li><input type="checkbox" name="contact_3_1" value="true"> GET all contacts [page and pagesize from config] with update_after parameter</li>
        <li><input type="checkbox" name="contact_4" value="true"> GET contact with ID and checksum from config</li>
        <li><input type="checkbox" name="contact_5" value="true"> PUT update contact with ID and checksum from config</li>
        <li><input type="checkbox" name="contact_5_1" value="true"> PUT update contact with its email as identifier</li>
        <li><input type="checkbox" name="contact_5_2" value="true"> PUT update contact with its external ID as identifier</li>
        <li><input type="checkbox" name="contact_6" value="true"> DELETE contact by email from config</li>
        <li><input type="checkbox" name="contact_7" value="true"> GET all contacts [page and pagesize from config]</li>
        <li><input type="checkbox" name="contact_8" value="true"> GET contact with email from config</li>
        <li><input type="checkbox" name="contact_81" value="true"> GET contacts (!) with email from config</li>
        <li><input type="checkbox" name="contact_82" value="true"> GET contacts with external ID from config</li>
        <li><input type="checkbox" name="contact_83" value="true"> GET contacts matching a given filter</li>
        <li><input type="checkbox" name="contact_9" value="true"> DELETE contact by external ID from config</li>
        <li><input type="checkbox" name="contact_10" value="true"> DELETE unsubscribe contact by email from config</li>
        <li><input type="checkbox" name="contact_101" value="true"> DELETE unsubscribe contact by Maileon ID from config</li>
        <li><input type="checkbox" name="contact_102" value="true"> DELETE unsubscribe contact by external ID from config</li>
        <li><input type="checkbox" name="contact_103" value="true"> PUT add unsubscription reasons to an unsubscribed contact by Maileon ID from config</li>
        <li><input type="checkbox" name="contact_11" value="true"> GET custom options from contacts (generic list)</li>
        <li><input type="checkbox" name="contact_12" value="true"> POST synchronize contacts</li>
        <li><input type="checkbox" name="contact_13" value="true"> GET blocked contacts</li>
        <li><input type="checkbox" name="contact_14" value="true"> POST create custom field</li>
        <li><input type="checkbox" name="contact_15" value="true"> GET custom fields</li>
        <li><input type="checkbox" name="contact_16" value="true"> PUT rename custom field</li>
        <li><input type="checkbox" name="contact_17" value="true"> DELETE custom field</li>
        <li><input type="checkbox" name="contact_18" value="true"> DELETE standard field values</li>
        <li><input type="checkbox" name="contact_19" value="true"> DELETE custom field values</li>
        <li><input type="checkbox" name="contact_20" value="true"> DELETE custom field values and synchronize contacts with new values</li>
        <li><input type="checkbox" name="contact_21" value="true"> GET Contact Preference Categories</li>
        <li><input type="checkbox" name="contact_22" value="true"> POST Contact Preference Category from config</li>
        <li><input type="checkbox" name="contact_23" value="true"> GET Contact Preference Category from config</li>
        <li><input type="checkbox" name="contact_24" value="true"> PUT Contact Preference Category from config</li>
        <li><input type="checkbox" name="contact_25" value="true"> DELETE Contact Preference Category from config</li>
        <li><input type="checkbox" name="contact_26" value="true"> GET Preferences of Contact Preferences Category from config</li>
        <li><input type="checkbox" name="contact_27" value="true"> POST Contact Preference from config</li>
        <li><input type="checkbox" name="contact_28" value="true"> GET Contact Preference from config</li>
        <li><input type="checkbox" name="contact_29" value="true"> PUT Contact Preference from config</li>
        <li><input type="checkbox" name="contact_30" value="true"> DELETE Contact Preference from config</li>
    </ul>

    <h2>Contactfilter - Tests</h2>
    <ul>
        <li><input type="checkbox" name="contactfilter_1" value="true"> GET contactfilter count</li>
        <li><input type="checkbox" name="contactfilter_2" value="true"> GET all contactfilters</li>
        <li><input type="checkbox" name="contactfilter_3" value="true"> GET contactfilter with ID from config</li>
        <li><input type="checkbox" name="contactfilter_4" value="true"> POST change name of contact filter with ID from
            config to "Some-NEW-Name"
        </li>
        <li><input type="checkbox" name="contactfilter_5" value="true"> GET update the contacts that are covered by that
            filter
        </li>
        <li><input type="checkbox" name="contactfilter_6" value="true"> PUT create a contact filter</li>
        <li><input type="checkbox" name="contactfilter_7" value="true"> DELETE contact filter</li>
    </ul>
    </ul>

    <h2>TargetGroup - Tests</h2>
    <ul>
        <li><input type="checkbox" name="targetgroup_1" value="true"> GET target groups count</li>
        <li><input type="checkbox" name="targetgroup_2" value="true"> GET all target groups</li>
        <li><input type="checkbox" name="targetgroup_3" value="true"> GET target group</li>
        <li><input type="checkbox" name="targetgroup_4" value="true"> POST create target group</li>
        <li><input type="checkbox" name="targetgroup_5" value="true"> DELETE target group</li>
    </ul>

    <h2>Mailings - Tests</h2>
    <ul>
        <li><input type="checkbox" name="mailings_1" value="true"> GET get HTML of mailing with ID from config</li>
        <li><input type="checkbox" name="mailings_2" value="true"> POST set HTML of mailing with ID from config</li>
        <li><input type="checkbox" name="mailings_3" value="true"> GET get last 10 mailings</li>
        <li><input type="checkbox" name="mailings_4_1" value="true"> GET sender alias</li>
        <li><input type="checkbox" name="mailings_4" value="true"> GET sender</li>
        <li><input type="checkbox" name="mailings_5" value="true"> POST set sender</li>
        <li><input type="checkbox" name="mailings_6" value="true"> GET get reply-to</li>
        <li><input type="checkbox" name="mailings_7" value="true"> POST reply-to address</li>
        <li><input type="checkbox" name="mailings_8" value="true"> POST create regular mailing</li>
        <li><input type="checkbox" name="mailings_9" value="true"> POST create trigger mailing</li>
        <li><input type="checkbox" name="mailings_10" value="true"> POST create doi mailing</li>
        <li><input type="checkbox" name="mailings_11" value="true"> PUT create dispatching</li>
        <li><input type="checkbox" name="mailings_12" value="true"> DELETE delete mailing</li>
        <li><input type="checkbox" name="mailings_13" value="true"> DELETE deactivate trigger mailing</li>
        <li><input type="checkbox" name="mailings_14" value="true"> GET dispatch scheduling for trigger mailing</li>
        <li><input type="checkbox" name="mailings_15" value="true"> POST copy mailing</li>
        <li><input type="checkbox" name="mailings_15_0" value="true"> GET schedule for regular mailing</li>
        <li><input type="checkbox" name="mailings_15_1" value="true"> PUT schedule mailing</li>
        <li><input type="checkbox" name="mailings_15_2" value="true"> POST update schedule of mailing</li>
        <li><input type="checkbox" name="mailings_15_3" value="true"> PUT Set the schedule for regular mailing with advanced dispatch options</li>
        <li><input type="checkbox" name="mailings_15_4" value="true"> POST Update the schedule for regular mailing with advanced dispatch options</li>
        <li><input type="checkbox" name="mailings_16" value="true"> PUT disable all QoS checks</li>
        <li><input type="checkbox" name="mailings_17" value="true"> GET get the archive URL</li>
        <li><input type="checkbox" name="mailings_18" value="true"> POST and GET tags</li>
        <li><input type="checkbox" name="mailings_19" value="true"> POST and GET locale</li>
        <li><input type="checkbox" name="mailings_20" value="true"> POST fill RSS SmartMailing Tags</li>
        <li><input type="checkbox" name="mailings_21" value="true"> GET check existence of mailing and getting mailing ID</li>
        <li><input type="checkbox" name="mailings_22" value="true"> GET customProperties for mailing</li>
        <li><input type="checkbox" name="mailings_23" value="true"> POST add customProperty for mailing</li>
        <li><input type="checkbox" name="mailings_24" value="true"> PUT update customProperty for mailing</li>
        <li><input type="checkbox" name="mailings_25" value="true"> DELETE delete customProperty for mailing</li>
        <li><input type="checkbox" name="mailings_26" value="true"> POST send test mail to single contact for mailing</li>
        <li><input type="checkbox" name="mailings_27" value="true"> POST send test mail to test group for mailing</li>
        <li><input type="checkbox" name="mailings_28" value="true"> GET report URL for mailing</li>
        <li><input type="checkbox" name="mailings_29" value="true"> GET mailing template</li>
        <li><input type="checkbox" name="mailings_30" value="true"> GET mailing preview text</li>
        <li><input type="checkbox" name="mailings_31" value="true"> SET mailing preview text</li>
        <li><input type="checkbox" name="mailings_32" value="true"> GET mailing subject</li>
        <li><input type="checkbox" name="mailings_33" value="true"> GET mailing cleanup option</li>
        <li><input type="checkbox" name="mailings_34" value="true"> SET mailing cleanup option</li>
        <li><input type="checkbox" name="mailings_35" value="true"> SET mailing blacklist</li>
        <li><input type="checkbox" name="mailings_36" value="true"> GET mailing blacklists</li>
        <li><input type="checkbox" name="mailings_37" value="true"> DELETE mailing blacklist</li>
        <li><input type="checkbox" name="mailings_38" value="true"> GET mailing domain</li>
        <li><input type="checkbox" name="mailings_39" value="true"> GET mailing contact filter restrictions count</li>
        <li><input type="checkbox" name="mailings_40" value="true"> POST add mailing contact filter restriction</li>
        <li><input type="checkbox" name="mailings_41" value="true"> DELETE remove contact filter restriction</li>
    </ul>

    <h2>Mailings CMS2 - Tests</h2>
    <ul>
        <li><input type="checkbox" name="mailings_cms2_1" value="true"> POST CMS2 grab the images for the mailing with ID from config</li>
        <li><input type="checkbox" name="mailings_cms2_2" value="true"> GET CMS2 mailing as ZIP for the mailing with ID from config</li>
        <li><input type="checkbox" name="mailings_cms2_3" value="true"> POST CMS2 save mailing with ID from config to media library</li>
        <li><input type="checkbox" name="mailings_cms2_4" value="true"> PUT CMS2 set template for mailing with ID from config from the media library</li>
    </ul>

    <h2>Media - Tests</h2>

    <p>
        <input type="checkbox" name="media_1" value="true"> GET a list of mailing templates
    </p>

    <h2>Reports - Tests</h2>
    <ul>
        <li><input type="checkbox" name="reports_1" value="true"> GET unsubscribers</li>
        <li><input type="checkbox" name="reports_1_1" value="true"> GET unsubscriber reasons</li>
        <li><input type="checkbox" name="reports_2" value="true"> GET subscribers</li>
        <li><input type="checkbox" name="reports_3" value="true"> GET recipients</li>
        <li><input type="checkbox" name="reports_4" value="true"> GET opens</li>
        <li><input type="checkbox" name="reports_5" value="true"> GET unique opens</li>
        <li><input type="checkbox" name="reports_6" value="true"> GET clicks</li>
        <li><input type="checkbox" name="reports_7" value="true"> GET unique clicks</li>
        <li><input type="checkbox" name="reports_8" value="true"> GET bounces</li>
        <li><input type="checkbox" name="reports_9" value="true"> GET unique bounces</li>
        <li><input type="checkbox" name="reports_10" value="true"> GET blocks</li>
        <li><input type="checkbox" name="reports_11" value="true"> GET conversions</li>
        <li><input type="checkbox" name="reports_12" value="true"> GET unique conversions</li>
    </ul>

    <h2>Transactions - Tests</h2>
    <ul>
        <li><input type="checkbox" name="transactions_1" value="true"> GET get transactions types count</li>
        <li><input type="checkbox" name="transactions_2" value="true"> GET get a list of transaction types</li>
        <li><input type="checkbox" name="transactions_3" value="true"> GET transaction type</li>
        <li><input type="checkbox" name="transactions_4" value="true"> POST create transaction type</li>
        <li><input type="checkbox" name="transactions_4_1" value="true"> POST create complex transaction type</li>
        <li><input type="checkbox" name="transactions_4_2" value="true"> POST create transaction type with archivalDuration</li>
        <li><input type="checkbox" name="transactions_5" value="true"> DELETE transaction type</li>
        <li><input type="checkbox" name="transactions_6" value="true"> POST create transactions</li>
        <li><input type="checkbox" name="transactions_6_1" value="true"> POST create complex transactions</li>
        <li><input type="checkbox" name="transactions_6_2" value="true"> POST create 10 transactions fast</li>
        <li><input type="checkbox" name="transactions_6_3" value="true"> POST create transactions with TYPE NAME and TRANSACTION ID from config</li>
        <li><input type="checkbox" name="transactions_7" value="true"> POST create transactions and create non existing contact</li>
        <li><input type="checkbox" name="transactions_8" value="true"> POST create transactions with attachment</li>
        <li><input type="checkbox" name="transactions_9" value="true"> DELETE transactions before a given date</li>
        <li><input type="checkbox" name="transactions_10" value="true"> GET recent transactions</li>
        <li><input type="checkbox" name="transactions_11" value="true"> GET transaction with TYPE ID and TRANSACTION ID from config </li>
        <li><input type="checkbox" name="transactions_12" value="true"> DELETE transaction with TYPE ID and TRANSACTION ID from config </li>
    </ul>

    <h2>Blacklists - Tests</h2>
    <ul>
        <li><input type="checkbox" name="blacklists_1" value="true"> GET get blacklists</li>
        <li><input type="checkbox" name="blacklists_2" value="true"> GET get blacklist</li>
        <li><input type="checkbox" name="blacklists_3" value="true"> POST add entries to blacklist</li>
    </ul>

    <h2>Mailing Blacklists - Tests</h2>
    <ul>
        <li><input type="checkbox" name="mailing_blacklists_1" value="true"> GET get mailing blacklists</li>
        <li><input type="checkbox" name="mailing_blacklists_2" value="true"> GET get a mailing blacklist</li>
        <li><input type="checkbox" name="mailing_blacklists_3" value="true"> POST create a mailing blacklist</li>
        <li><input type="checkbox" name="mailing_blacklists_4" value="true"> PUT update a mailing blacklist</li>
        <li><input type="checkbox" name="mailing_blacklists_5" value="true"> DELETE delete a mailing blacklist</li>
        <li><input type="checkbox" name="mailing_blacklists_6" value="true"> POST add entries to a mailing blacklist</li>
        <li><input type="checkbox" name="mailing_blacklists_7" value="true"> GET the entries from a mailing blacklist</li>
    </ul>

    <h2>Account - Tests</h2>
    <ul>
        <li><input type="checkbox" name="account_1" value="true"> GET a list of all account placeholders</li>
        <li><input type="checkbox" name="account_2" value="true"> POST set account placeholders</li>
        <li><input type="checkbox" name="account_3" value="true"> PUT update/add account placeholders</li>
        <li><input type="checkbox" name="account_4" value="true"> DELETE remove account placeholder</li>
    </ul>

    <h2>Webhook - Tests</h2>
    <ul>
        <li><input type="checkbox" name="webhook_1" value="true"> GET a webhook with ID from config</li>
        <li><input type="checkbox" name="webhook_2" value="true"> DELETE the webhook with ID from config</li>
        <li><input type="checkbox" name="webhook_3" value="true"> PUT update the webhook with ID from config</li>
        <li><input type="checkbox" name="webhook_4" value="true"> POST create a new webhook</li>
        <li><input type="checkbox" name="webhook_5" value="true"> GET a list of webhooks</li>
    </ul>

    <br/>
    <br/>
    <input type="submit" value=" Run tests ">
    <input type="reset" value=" Cancel ">
</form>


</body>
</html>