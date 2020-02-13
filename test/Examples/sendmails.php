<?php
    namespace Maileon\Test\Examples;

    use Maileon\HTTPResponseCodes;
    use Maileon\Contacts\ContactsService;
    use Maileon\Mailings\MailingsService;
    use Maileon\TargetGroups\TargetGroupsService;
    use Maileon\ContactFilters\ContactfiltersService;
    use Maileon\Contacts\Contact;
    use Maileon\Contacts\Contacts;
    use Maileon\ContactFilters\Rule;
    use Maileon\Contacts\Permission;
    use Maileon\ContactFilters\ContactFilter;
    use Maileon\Contacts\SynchronizationMode;

    // The maileon configuration
    $config = array(
        "BASE_URI" => "http://api.maileon.com/1.0",
        "API_KEY" => "xxx",
        "THROW_EXCEPTION" => "true",
        "DEBUG" => false, // NEVER enable on production
        "TIMEOUT" => 0
    );

    function checkResult($result = "")
    {
        // What is retruned? can it be null?
        $statusCode = $result->getStatusCode();
        if (!$result->isSuccess()) {
            echo '<font color="#ff0000"><b>failed</b></font>' . ' (Status code: ' . $statusCode . ' - '
                . HTTPResponseCodes::getStringFromHTTPStatusCode($statusCode)
                . ($result->getBodyData() ? ' - ' . $result->getBodyData() : '') . ')';
        } else {
            echo '<font color="#22ff22"><b>success</b></font>' . ' (Status code: ' . $statusCode . ' - '
                . HTTPResponseCodes::getStringFromHTTPStatusCode($statusCode)  . ')';
        }
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Maileon Transactions Testpage</title>
</head>
<body>

<?php

    $contactFieldName = "ApiTestField";
    $contactFilterName = "ApiTestfilter";

    $contactsService = new ContactsService($config);
    $contactsService->setDebug($config['DEBUG']);

    $contactfiltersService = new ContactfiltersService($config);
    $contactfiltersService->setDebug($config['DEBUG']);

    $targetgroupsService = new TargetGroupsService($config);
    $targetgroupsService->setDebug($config['DEBUG']);

    $mailingService = new MailingsService($config);
    $mailingService->setDebug($config['DEBUG']);

?>

<h1>Full Example</h1>

<ul>
    <li>
        <b>Creating Custom Contactfield &quot;<?=$contactFieldName?>&quot;</b>:
        <?php
            $response = $contactsService->createCustomField($contactFieldName, "string");
            checkResult($response);
        ?>
    </li>


    <li>
        <b>Creating Contacts</b>:
        <?php
            $contactsToSync = new Contacts();

            $contactsToSync->addContact(new Contact(
                null,
                'marcus.staender@xqueue.de',
                null,
                null,
                $anonymous = null,
                array(
                    'LASTNAME' => 'Marcus1',
                    'FIRSTNAME' => 'Ständer1'
                ),
                array(
                    $contactFieldName => "true"
                )
            ));

            $contactsToSync->addContact(new Contact(
                null,
                'marcus.staender@xqueue.com',
                null,
                null,
                $anonymous = null,
                array(
                    'LASTNAME' => 'Marcus2',
                    'FIRSTNAME' => 'Ständer2'
                ),
                array(
                    $contactFieldName => "true"
                )
            ));

            $useExternalId = false;
            $ignoreInvalidContacts = true;
            $reimportUnsubscribedContacts = true;
            $overridePermission = true;
            $updateOnly = false;

            $response = $contactsService->synchronizeContacts(
                $contactsToSync,
                Permission::$DOI_PLUS,
                SynchronizationMode::$UPDATE,
                $useExternalId,
                $ignoreInvalidContacts,
                $reimportUnsubscribedContacts,
                $overridePermission,
                $updateOnly
            );

            checkResult($response);
        ?>
    </li>


    <li>
        <b>Creating Contactfilter and targetgroup &quot;<?=$contactFilterName?>&quot;</b>:
        <?php
        $filter = new ContactFilter(0, $contactFilterName, "xq-freiburg@xqueue.com");
        $filter->addRule(new Rule(true, $contactFieldName, "EQUALS", "true"));
        $response = $contactfiltersService->createContactFilter($filter, true);

    // echo "<pre>";
    //echo $filter->toXML()->asXml();
    // echo "</pre>";

        $targetGroupId = 0;
        $contactFilterId = 0;

        checkResult($response);

        if ($response->isSuccess()) {
            $targetGroupId = $response->getResult()['target_group_id'];
            $contactFilterId = $response->getResult()['contact_filter_id'];
            echo " [targetGroupId=$targetGroupId, contactFilterId=$contactFilterId]";
        }
        ?>

    </li>
    <li>
        <b>Creating and sending mailing</b>:
        <?php
        $name= "APITest".generateRandomString();
        $subject = "Marcus API Test";
        $response = $mailingService->createMailing($name . "_regular", $subject, true, "regular");

        $mailingId = $response->getResult();

        $mailingService->setHTMLContent($mailingId, "<html><head></head><body><a href=\"http://google.com\">Hello World!</a><a href=\"http://google.com/1\">Hello World!</a><a href=\"http://google.com/2\">Hello World!</a><a href=\"http://google.com/3\">Hello World!</a><a href=\"http://google.com\">Hello World!</a><a href=\"http://google.com/1\">Hello World!</a><a href=\"http://google.com/2\">Hello World!</a><a href=\"http://google.com/3\">Hello World!</a></body></html>", true, true);
        $mailingService->setTextContent($mailingId, "Hallo Welt!");

        $mailingService->setSender($mailingId, "xqueue");
        $mailingService->setTargetGroupId($mailingId, $targetGroupId); // the ID of the target group

        $response = $mailingService->sendMailingNow($mailingId);

        checkResult($response);
        ?>

    </li>



    <li>
        <b>Waiting 30 seconds</b>:
        <?php
        echo sleep(30) . " (0 is good)";
        ?>

    </li>
    <li>
        <b>Cleaning up</b>:
        <ul>
            <?php

            // First delete target group to free filter
            if (!empty($targetGroupId)) {
                $response = $targetgroupsService->deleteTargetGroup($targetGroupId);
                echo "<li>Removing target group: ";
                echo checkResult($response);
                echo "</li>";
            }

            // then delete filter to free contact field
            if (!empty($contactFilterId)) {
                $response = $contactfiltersService->deleteContactFilter($contactFilterId);
            }
            echo "<li>Removing contact filter: ";
            echo checkResult($response);
            echo "</li>";

            // then delete custom field
            $response = $contactsService->deleteCustomField($contactFieldName);
            echo "<li>Removing contact field: ";
            echo checkResult($response);
            echo "</li>";

            ?>
        </ul>
    </li>
</ul>
</body>
</html>