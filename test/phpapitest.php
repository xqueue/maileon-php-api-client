<?php

require "vendor/autoload.php";

use de\xqueue\maileon\api\client\HTTPResponseCodes;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\StandardContactField;
use de\xqueue\maileon\api\client\contactfilters\ContactFilter;
use de\xqueue\maileon\api\client\account\AccountPlaceholder;
use de\xqueue\maileon\api\client\Utils\PingService;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use de\xqueue\maileon\api\client\contacts\Contacts;
use de\xqueue\maileon\api\client\contactfilters\ContactfiltersService;
use de\xqueue\maileon\api\client\contactfilters\Rule;
use de\xqueue\maileon\api\client\targetgroups\TargetGroupsService;
use de\xqueue\maileon\api\client\targetgroups\TargetGroup;
use de\xqueue\maileon\api\client\mailings\MailingsService;
use de\xqueue\maileon\api\client\mailings\MailingFields;
use de\xqueue\maileon\api\client\mailings\CustomProperty;
use de\xqueue\maileon\api\client\media\MediaService;
use de\xqueue\maileon\api\client\reports\ReportsService;
use de\xqueue\maileon\api\client\transactions\TransactionsService;
use de\xqueue\maileon\api\client\transactions\AttributeType;
use de\xqueue\maileon\api\client\transactions\Transaction;
use de\xqueue\maileon\api\client\transactions\ContactReference;
use de\xqueue\maileon\api\client\transactions\ImportReference;
use de\xqueue\maileon\api\client\transactions\ImportContactReference;
use de\xqueue\maileon\api\client\Blacklists\BlacklistsService;
use de\xqueue\maileon\api\client\account\AccountService;
use de\xqueue\maileon\api\client\transactions\TransactionType;
use de\xqueue\maileon\api\client\transactions\DataType;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!@include("./conf/config.php")) {
    die("'conf/config.php' not found. Make sure you created a config file and placed it there.");
}
if (!@include("./conf/testdata.include")) {
    die("'conf/testdata.include' not found. Make sure you copied it there.");
}

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

// Not really required but used as the classloader currently prints first time a class is loaded and it looks ugly if the class is loaded in between
$notUsed = HTTPResponseCodes::getStringFromHTTPStatusCode(200);

$debug = isset($_POST['debug']) && $_POST['debug']==="true";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<style type="text/css">
pre {
    white-space: pre-wrap;       /* css-3 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;   
}
</style>
<title>Maileon PHP API Client Testpage</title>
</head>
<body>
<a href="index.php"><img src="media/xqueue.jpg" alt="XQueue"></a>

<h1>Maileon API Client Testpage</h1>



    <?php
    // ----------------------------------------------------------------------------------------------------------
    // Ping
    // ----------------------------------------------------------------------------------------------------------

    if (containsPostNeedle("ping_")) {
    ?>

<h2>Ping - Tests</h2>

    <?php
        $pingService = new PingService($config);
        $pingService->setDebug($debug);
    ?>

<ul>
<?php if (isset($_POST['ping_1'])) { ?>
<li>
GET:
<?php
	$response = $pingService->pingGet();
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['ping_2'])) { ?>
<li>
PUT:
<?php
	$response = $pingService->pingPut();
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['ping_3'])) { ?>
<li>
POST:
<?php
	$response = $pingService->pingPost();
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['ping_4'])) { ?>
<li>
DELETE:
<?php
	$response = $pingService->pingDelete();
	checkResult($response);
?>
</li>
<?php } ?>
</ul>
<?php } // End?>



<?php
// ----------------------------------------------------------------------------------------------------------
// Contacts
// ----------------------------------------------------------------------------------------------------------

if (containsPostNeedle("contact_")) {
?>

<h2>Contact - Tests</h2>

<?php
	$contactsService = new ContactsService($config);
	$contactsService->setDebug($debug);

	// Not really required but used as the classloader currently prints first time a class is loaded and it looks ugly if the class is loaded in between
	$notUsed = new Contact();
?>

<ul>
<?php if (isset($_POST['contact_1'])) { ?>
<li>
POST Create contact:
<?php

	$newContact = new Contact();
	$newContact->anonymous = false;
	$newContact->email = "max.mustermann@xqueue.com";//$TESTDATA['userEmail'];
	$newContact->external_id = $TESTDATA['external_id'];
	$newContact->permission = Permission::$SOI;

	//$newContact->custom_fields['someKey'] = "someVal";
	$newContact->standard_fields[StandardContactField::$FIRSTNAME] = "max";
	$newContact->standard_fields[StandardContactField::$LASTNAME] = "mustermann";
	$newContact->standard_fields[StandardContactField::$ZIP] = "1337";
	$newContact->standard_fields[StandardContactField::$SALUTATION] = "Herr";
	$newContact->standard_fields[StandardContactField::$COUNTRY] = "De";
	$newContact->standard_fields[StandardContactField::$ADDRESS] = "Christian-Pless-Straße";
	$newContact->standard_fields[StandardContactField::$CITY] = "Offenbach";
	$newContact->standard_fields[StandardContactField::$HNR] = "11-13";
	$newContact->standard_fields[StandardContactField::$BIRTHDAY] = "04.06.1982";

	//$newContact->custom_fields["Kundenart"] = "Newsletter-Empfänger";
	//$newContact->custom_fields["Firmenname"] = "XQueue";
//	$newContact->custom_fields["Telefonnummer"] = "069-830089820";
	
	$response = $contactsService->createContact($newContact, SynchronizationMode::$UPDATE, null, "test subscriptionPage", true, false);//, $TESTDATA['doiMailingKey']);
	checkResult($response);

	echo "<pre><ul><li>".$newContact->toXMLString()."</li></ul></pre>";
	
	echo "Clone test (print cloned object):";
				
	$nc = new Contact();
	$nc->fromXMLString($newContact->toXMLString());
	echo "<pre><ul><li>" . $nc->toString() . "</li></ul></pre>";
?>
</li>
<?php } if (isset($_POST['contact_1_1'])) { ?>
<li>
POST Create contact:
<?php

	$newContact = new Contact();
	$newContact->anonymous = false;
	$newContact->email = "z".$TESTDATA['userEmail']."X";
	$newContact->external_id = $TESTDATA['external_id']."x";
	$newContact->permission = Permission::$NONE;

	//$newContact->custom_fields['someKey'] = "someVal";
	$newContact->standard_fields[StandardContactField::$FIRSTNAME] = "FirstName";
	$newContact->standard_fields[StandardContactField::$LASTNAME] = "<test>";
	
	$response = $contactsService->createContactByExternalId($newContact, SynchronizationMode::$UPDATE, "src", "subscriptionPage", true, true, $TESTDATA['doiMailingKey']);
	checkResult($response);
?>
</li>

<?php } if (isset($_POST['contact_1_2'])) { ?>
	<li>
		PUT Update contact:
		<?php

		$updateContact = new Contact();
		$updateContact->anonymous = false;
		$updateContact->id = 1328960;
		$updateContact->email = "max.mustermann@xqueue.com";
		$updateContact->permission = Permission::$NONE;

		//$newContact->custom_fields['someKey'] = "someVal";
		$updateContact->standard_fields[StandardContactField::$FIRSTNAME] = "FirstName";
		$updateContact->standard_fields[StandardContactField::$LASTNAME] = "Test";

		//$updateContact->custom_fields['Medical'] = "true";
		//$updateContact->custom_fields['Office, Fotografie, Gaming, Industrie'] = "false";

		$response = $contactsService->updateContact($updateContact, "", "src", "subscriptionPage", true, null, true);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['contact_2'])) { ?>
<li>
GET contacts count:
<?php
	$response = $contactsService->getContactsCount();
	checkResult($response);
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned contacts count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>
<?php } if (isset($_POST['contact_3'])) { ?>
<li>
GET all contacts [page <?= $TESTDATA['page_index']?>, pagesize <?= $TESTDATA['page_size']?>]:
<?php
//array('FIRSTNAME','LASTNAME')

	$start = time();
	$response = $contactsService->getContacts($TESTDATA['page_index'], $TESTDATA['page_size'], array('FIRSTNAME', 'LASTNAME'), array());
	checkResult($response);
	$end = time();

	echo "<br />Duration: ".($end - $start)." seconds<br />";

	echo "<br />Headers: ";
	var_dump($response->getResponseHeaders());
	echo "<br />";

	echo $response->getResponseHeaders()['X-Pages'];

	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
			foreach ($response->getResult() as $contact) {
				echo "<li>" . $contact->toString() . "</li>";
			}
		echo "</ul></pre>";
	}
?>
</li>
<?php } if (isset($_POST['contact_4'])) { ?>
<li>
GET contact with ID <?= $TESTDATA['userId']?> (ignore checksum):
<?php
	$response = $contactsService->getContact($TESTDATA['userId'], null, array('FIRSTNAME','LASTNAME'), array(), true);
	checkResult($response);
	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li>" . $response->getResult()->toString() . "</li></ul>";
	}
?>
</li>
<?php } if (isset($_POST['contact_5'])) { ?>
<li>
PUT update contact with ID <?= $TESTDATA['userId']?> (ignore checksum):
<?php

	$newUpdateContact = new Contact();
	$newUpdateContact->id = 196837;
//	$newUpdateContact->external_id = $TESTDATA['userExternalId']."_updated";
	$newUpdateContact->standard_fields[StandardContactField::$FIRSTNAME] = "max";
	
	echo "\n<pre>" . htmlentities($newUpdateContact->toXMLString()) . "</pre>\n";

	//$response = $contactsService->updateContact($newUpdateContact, $TESTDATA['userChecksum'], null, null, false, null, true);
	$response = $contactsService->updateContact($newUpdateContact, "Dkp-gA8sZ4A", null, null, false, null, false);

	checkResult($response);
	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li><pre>" . htmlentities($response->getResult()) . "</pre></li></ul>";
	}
?>
</li>
<?php } if (isset($_POST['contact_5_1'])) { ?>
<li>
PUT update contact with its email as identifier:
<?php

	$newUpdateContact = new Contact();
	$newUpdateContact->email = "maxi.mustermann@xqueue.com";
	$newUpdateContact->permission = Permission::$OTHER; // Not mandatory
	$newUpdateContact->standard_fields[StandardContactField::$FIRSTNAME] = "maxi";
	
	echo "\n<pre>" . htmlentities($newUpdateContact->toXMLString()) . "</pre>\n";

	$response = $contactsService->updateContactByEmail("maxi.mustermann@baunzt.de", $newUpdateContact);

	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contact_5_2'])) { ?>
<li>
PUT update contact with its external ID as identifier:
<?php

	$newUpdateContact = new Contact();
	$newUpdateContact->external_id = "test123";
	$newUpdateContact->email = "maxi2.mustermann@baunzt.de";
	$newUpdateContact->permission = Permission::$NONE; // Not mandatory
	$newUpdateContact->standard_fields[StandardContactField::$FIRSTNAME] = "maxi external test";
	
	echo "\n<pre>" . htmlentities($newUpdateContact->toXMLString()) . "</pre>\n";

	$response = $contactsService->updateContactByExternalId("test123", $newUpdateContact);

	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contact_6'])) { ?>
<li>
DELETE contact with email: <?= $TESTDATA['userEmail']?>
<?php
	$response = $contactsService->deleteContactByEmail($TESTDATA['userEmail']);
	checkResult($response);
	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li><pre>" . htmlentities($response->getResult()) . "</pre></li></ul>";
	}
?>
</li>
<?php } if (isset($_POST['contact_7'])) { ?>
<li>
GET all contacts [page <?= $TESTDATA['page_index']?>, pagesize <?= $TESTDATA['page_size']?>]:
<?php
	$response = $contactsService->getContacts($TESTDATA['page_index'], $TESTDATA['page_size'], array('FIRSTNAME','LASTNAME'), array('abär'));
	checkResult($response);
	
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
			foreach ($response->getResult() as $contact) {
				echo "<li>" . $contact->toString() . "</li>";
			}
		echo "</ul></pre>";
	}
?>
</li>
<?php } if (isset($_POST['contact_8'])) { ?>
<li>
GET contact with emailadress [page <?= $TESTDATA['getEmailNotExisting']?>, pagesize <?= $TESTDATA['page_size']?>]:
<?php
	$response = $contactsService->getContactByEmail($TESTDATA['getEmailNotExisting'], array('FIRSTNAME','LASTNAME'));
	checkResult($response);

	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li>" . $response->getResult()->toString() . "</li></ul></pre>";
	}

	$response = $contactsService->getContactByEmail($TESTDATA['getEmail'], array('FIRSTNAME','LASTNAME'));
	checkResult($response);

	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li>" . $response->getResult()->toString() . "</li></ul></pre>";
	}
?>
</li>
<?php } if (isset($_POST['contact_81'])) { ?>
	<li>
		GET contacts (!) with emailadress [page <?= $TESTDATA['getEmailNotExisting']?>, pagesize <?= $TESTDATA['page_size']?>]:
		<?php
		$response = $contactsService->getContactsByEmail($TESTDATA['getEmailNotExisting'], array('FIRSTNAME','LASTNAME'));
		checkResult($response);

		if ($response->isSuccess()) {
			echo "<br /><pre><ul><li>" . $response->getResult()->toString() . "</li></ul></pre>";
		}
		?>
		GET contacts (!) with emailadress [page <?= $TESTDATA['getEmail']?>, pagesize <?= $TESTDATA['page_size']?>]:
		<?php
		$response = $contactsService->getContactsByEmail($TESTDATA['getEmail'], array('FIRSTNAME','LASTNAME'));
		checkResult($response);

		if ($response->isSuccess()) {
			echo "<br /><pre><ul><li>" . $response->getResult()->toString() . "</li></ul></pre>";
		}
		?>
	</li>
<?php } if (isset($_POST['contact_82'])) { ?>
<li>
GET contacts with external ID [page <?= $TESTDATA['userExternalId']?>, pagesize <?= $TESTDATA['page_size']?>]:
<?php
//	$response = $contactsService->getContactsByExternalId($TESTDATA['userExternalId'], array('FIRSTNAME','LASTNAME'), array('Main ID', 'CV ID', 'Kategoria', 'Port�l'));
	$response = $contactsService->getContactsByExternalId($TESTDATA['userExternalId']);
	checkResult($response);
	
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $contact) {
			echo "<li>" . $contact->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
</li>
<?php } if (isset($_POST['contact_83'])) { ?>
<li>
GET contacts matching filter [page <?= $TESTDATA['contactFilterId']?>]:
<?php
	$response = $contactsService->getContactsByFilterId($TESTDATA['contactFilterId']);
	checkResult($response);

	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $contact) {
			echo "<li>" . $contact->toString() . "</li>";
		}
		echo "</ul></pre>";
	}

	$response = $contactsService->getCountContactsByFilterId($TESTDATA['contactFilterId']);
	checkResult($response);

	$response = $contactsService->getCountActiveContactsByFilterId($TESTDATA['contactFilterId']);
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contact_9'])) { ?>
<li>
DELETE contact with external ID <?= $TESTDATA['userExternalId'] ?>:
<?php
	$response = $contactsService->deleteContactsByExternalId($TESTDATA['userExternalId']);
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contact_10'])) { ?>
<li>
DELETE unsubscribe contact with email <?= $TESTDATA['userEmail'] ?>:
<?php
	$response = $contactsService->unsubscribeContactByEmail("max.mustermann@xqueue.com", null, array("too_many_messages", "Deployment-Tests rock."));
//	$response = $contactsService->unsubscribeContactByEmail($TESTDATA['userEmail'], null, array("too_many_messages", "Deployment-Tests rock."));
	//$response = $contactsService->unsubscribeContactByExternalId("test");
	checkResult($response);
	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li>" . htmlentities($response->getResult()) . "</li></ul>";
	}
?>
</li>
<?php } if (isset($_POST['contact_101'])) { ?>
	<li>
		DELETE unsubscribe contact with maileon id <?= $TESTDATA['userId'] ?>:
		<?php
		//$response = $contactsService->unsubscribeContactById($TESTDATA['userId']);
		$response = $contactsService->unsubscribeContactById($TESTDATA['userId'], null, array("api reason 1", "api reason 2", "api reason 3"));
		checkResult($response);
		if ($response->isSuccess()) {
			echo "<br /><pre><ul><li>" . htmlentities($response->getResult()) . "</li></ul>";
		}
		?>
	</li>
<?php } if (isset($_POST['contact_102'])) { ?>
	<li>
		DELETE unsubscribe contact with external id <?= $TESTDATA['userExternalId'] ?>:
		<?php
		$response = $contactsService->unsubscribeContactByExternalId($TESTDATA['userExternalId'], null, array("api reason 1", "api reason 2"));
		//$response = $contactsService->unsubscribeContactByExternalIdFromMultipleAccounts($TESTDATA['userExternalId'], array(1, 2), array("api reason 1", "api reason 2"));
		checkResult($response);
		if ($response->isSuccess()) {
			echo "<br /><pre><ul><li>" . htmlentities($response->getResult()) . "</li></ul>";
		}
		?>
	</li>
<?php } if (isset($_POST['contact_103'])) { ?>
	<li>
		PUT add unsubscription reasons to an unsubscribed contact by Maileon ID from config: <?= $TESTDATA['userId'] ?>:
		<?php
		$response = $contactsService->addUnsubscriptionReasonsToUnsubscribedContact($TESTDATA['userId'], "OXhKHuRokqg", "other|I don't like newsletters at all", true);
//		$response = $contactsService->addUnsubscriptionReasonsToUnsubscribedContact($TESTDATA['userId'], "OXhKHuRokqg", array("other|I don't like newsletters at all", "change_of_interest", "technical_reasons|I cannot receive HTML mails"), true);
		checkResult($response);
		if ($response->isSuccess()) {
			echo "<br /><pre><ul><li>" . htmlentities($response->getResult()) . "</li></ul>";
		}
		?>
	</li>
<?php } if (isset($_POST['contact_11'])) { ?>
<li>
GET custom options from contacts (generic list):
<?php
	$response = $contactsService->getCustomFields();
	checkResult($response);
	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li>" . $response->getResult()->toString() . "</li></ul>";
	}
?>
</li>
<?php } if (isset($_POST['contact_12'])) { ?>
<li>
 POST synchronize contacts:
<?php

	$useExternalId = false;
	$ignoreInvalidContacts = true;
	$reimportUnsubscribedContacts = true;
	$overridePermission = false;
	$updateOnly = false;

	$response = $contactsService->synchronizeContacts(
		$contactsToSync,
		Permission::$NONE,
		SynchronizationMode::$UPDATE,
		$useExternalId,
		$ignoreInvalidContacts,
		$reimportUnsubscribedContacts,
		$overridePermission,
		$updateOnly);
	checkResult($response);
	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li>" . htmlentities($response->getResult()) . "</li></ul>";
	}
?>
</li>
<?php } if (isset($_POST['contact_13'])) { ?>
<li>
GET blocked contacts [page <?= $TESTDATA['userExternalId']?>, pagesize <?= $TESTDATA['page_size']?>]::
<?php
	$response = $contactsService->getBlockedContacts(array('FIRSTNAME','LASTNAME'), array('COLOR'));
	checkResult($response);
	
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $contact) {
			echo "<li>" . $contact->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
</li>
<?php } if (isset($_POST['contact_14'])) { ?>
<li>
POST create custom field <?= $TESTDATA['field_name1'] ?> &#61;&gt; <?= $TESTDATA['field_type1'] ?>::
<?php
	$response = $contactsService->createCustomField($TESTDATA['field_name1'],$TESTDATA['field_type1']);
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contact_15'])) { ?>
<li>
GET custom fields::
<?php
	$response = $contactsService->getCustomFields();
	checkResult($response);

	if ($response->isSuccess()) {
		echo "<br /><pre>";
		echo $response->getResult()->toString();
		echo "</pre>";
	}
?>
</li>
<?php } if (isset($_POST['contact_16'])) { ?>
<li>
PUT rename custom field <?= $TESTDATA['field_name1'] ?> &#61;&gt; <?= $TESTDATA['field_name2'] ?> ::
<?php
	$response = $contactsService->renameCustomField($TESTDATA['field_name1'], $TESTDATA['field_name2']);
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contact_17'])) { ?>
<li>
DELETE custom field <?= $TESTDATA['field_name1'] ?>::
<?php
	$response = $contactsService->deleteCustomField($TESTDATA['field_name1']);
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contact_18'])) { ?>
<li>
DELETE values of standard field <?= $TESTDATA['field_name1'] ?>::
<?php
	$response = $contactsService->deleteStandardFieldValues($TESTDATA['stdfield_name1']);
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contact_19'])) { ?>
<li>
DELETE values of custom field <?= $TESTDATA['field_name1'] ?>::
<?php
	$response = $contactsService->deleteCustomFieldValues($TESTDATA['field_name1']);
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contact_20'])) { ?>
	<li>
		DELETE custom field values of field <?= $TESTDATA['custom_field_name_delete_values'] ?> and synchronize contacts with new values:
		<?php

		// Delete all contacts for testing purposes
		$response = $contactsService->deleteAllContacts();
		checkResult($response);

		// First add some contacts with a given custom field value
		$useExternalId = false;
		$ignoreInvalidContacts = true;
		$reimportUnsubscribedContacts = true;
		$overridePermission = false;
		$updateOnly = false;

		$contactsToSyncTmp = new Contacts();
		for ($i=0; $i<10000; $i++) {
			$contactsToSyncTmp->addContact(new Contact(null,
				'Xmax_'.$i.'.mustermann@baunzt.de',
				null,
				'external-id-'.$i,
				$anonymous = null,
				array(
					'LASTNAME' => 'Mustermann',
					'FIRSTNAME' => 'Max_'.$i
				),
				array($TESTDATA['custom_field_name_delete_values'] => "value_before")));
		}

		$response = $contactsService->synchronizeContacts(
			$contactsToSyncTmp,
			Permission::$NONE,
			SynchronizationMode::$UPDATE,
			$useExternalId,
			$ignoreInvalidContacts,
			$reimportUnsubscribedContacts,
			$overridePermission,
			$updateOnly);
		checkResult($response);


		// Now delete the values
		$response = $contactsService->deleteCustomFieldValues($TESTDATA['custom_field_name_delete_values']);
		checkResult($response);

		// And sync again, adding a value for every second contact only
		$contactsToSyncTmp = new Contacts();
		for ($i=0; $i<10000; $i++) {
			$contactsToSyncTmp->addContact(new Contact(null,
				'Xmax_'.$i.'.mustermann@baunzt.de',
				null,
				'external-id-'.$i,
				$anonymous = null,
				array(
					'LASTNAME' => 'Mustermann',
					'FIRSTNAME' => 'Max_'.$i
				),
				($i%2==0)?array($TESTDATA['custom_field_name_delete_values'] => "value_after"):array()));
		}

		$response = $contactsService->synchronizeContacts(
			$contactsToSyncTmp,
			Permission::$NONE,
			SynchronizationMode::$UPDATE,
			$useExternalId,
			$ignoreInvalidContacts,
			$reimportUnsubscribedContacts,
			$overridePermission,
			$updateOnly);
		checkResult($response);
		?>
	</li>
<?php } ?>
</ul>
<?php } // End?>




<?php
// ----------------------------------------------------------------------------------------------------------
// Contactfilters
// ----------------------------------------------------------------------------------------------------------

if (containsPostNeedle("contactfilter_")) {
?>

<h2>Contactfilter - Tests</h2>

<?php
	$contactfiltersService = new ContactfiltersService($config);
	$contactfiltersService->setDebug($debug);

	// Not really required but used as the classloader currently prints first time a class is loaded and it looks ugly if the class is loaded in between
	$notUsed = new ContactFilter();
?>

<ul>
<?php if (isset($_POST['contactfilter_1'])) { ?>
<li>
GET contactfilter count:
<?php
	$response = $contactfiltersService->getContactFiltersCount();
	checkResult($response);
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned contact filter count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>
<?php } if (isset($_POST['contactfilter_2'])) { ?>
<li>
GET all contactfilters:
<?php
	$response = $contactfiltersService->getContactFilters();
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
			foreach ($response->getResult() as $contactfilter) {
				echo "<li>" . $contactfilter->toString() . "</li>";
			}
		echo "</ul></pre>";
	}
?>
</li>
<?php } if (isset($_POST['contactfilter_3'])) { ?>
<li>
GET contactfilter with ID <?= $TESTDATA['contactFilterId']?>:
<?php
	$response = $contactfiltersService->getContactFilter($TESTDATA['contactFilterId']);
	checkResult($response);
	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li>" . $response->getResult()->toString() . "</li></ul>";
	}
?>
</li>
<?php } if (isset($_POST['contactfilter_4'])) { ?>
<li>
POST change name of contact filter with ID <?= $TESTDATA['contactFilterId']?> to "Some-NEW-Name":
<?php
	$filter = new ContactFilter($TESTDATA['contactFilterId'], "Some-NEW-Name", "xq-freiburg@xqueue.com", 12, 1, "2013-10-15 10:48:38", "uptodate");

	$response = $contactfiltersService->updateContactFilter(1, $filter);
	checkResult($response);
	
	$response = $contactfiltersService->getContactFilter(1);
	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li>" . $response->getResult()->toString() . "</li></ul>";
	}
?>
</li>
<?php } if (isset($_POST['contactfilter_5'])) { ?>
<li>
GET update the contacts that are covered by filter with ID <?= $TESTDATA['contactFilterId']?>:
<?php
	$response = $contactfiltersService->refreshContactFilterContacts($TESTDATA['contactFilterId'], time()*1000);
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['contactfilter_6'])) { ?>
	<li>
		PUT create contact filter v1.0:
		<?php

		$filter = new ContactFilter($TESTDATA['contactFilterId'], "testfilterNEU2", "xq-freiburg@xqueue.com");

		//$filter->addRule(new Rule(false, StandardContactField::$FIRSTNAME, "EQUALS", "max"));

		$filter->addRule(new Rule(true, "createdByTransaction", "EQUALS", "true", "boolean"));
		//$filter->addRule(new Rule(true, "Test123", "EQUALS", "weee"));

		echo "<pre>";
		echo $filter->toString();
		echo "</pre>";

		echo "<pre>";
		echo $filter->toXML()->asXml();
		echo "</pre>";


		$response = $contactfiltersService->createContactFilter($filter, false);
		checkResult($response);
		?>
	</li>
	<li>
		PUT create contact filter v2.0:
		<?php

		$filter = '
		{
		  "name": "My Contactfilter 12345",
		  "rules": [
			{
			  "startset": "empty",
			  "operation": "add",
			  "selection": {
				"selection_base": "contactfield",
				"criterion": {
				  "field_name": "email",
				  "operation": "includedin",
				  "value": [
					"max.mustermann@xqueue.com",
					"max2.mustermann@xqueue.com",
					"max3.mustermann@xqueue.com"
				  ]
				}
			  }
			}
		  ]
		}';


		$response = $contactfiltersService->createContactFilter($filter, false, 2.0);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['contactfilter_7'])) { ?>
	<li>
		DELETE delete contact filter:
		<?php

		$filter = new ContactFilter($TESTDATA['contactFilterId'], "testfilterNEU2", "xq-freiburg@xqueue.com");
		$filter->addRule(new Rule(false, StandardContactField::$FIRSTNAME, "EQUALS", "Max"));
		$response = $contactfiltersService->createContactFilter($filter, false);

		$response = $contactfiltersService->deleteContactFilter($response->getResult()['contact_filter_id']);
		checkResult($response);
		?>
	</li>
<?php } ?>
</ul>
<?php } ?>



<?php
// ----------------------------------------------------------------------------------------------------------
// Targetgroups
// ----------------------------------------------------------------------------------------------------------

if (containsPostNeedle("targetgroup_")) {
?>

<h2>TargetGroup - Tests</h2>

<?php
	$targetGroupsService = new TargetGroupsService($config);
	$targetGroupsService->setDebug($debug);

	// Not really required but used as the classloader currently prints first time a class is loaded and it looks ugly if the class is loaded in between
	$notUsed = new TargetGroup();
?>

<ul>
<?php if (isset($_POST['targetgroup_1'])) { ?>
<li>
GET target groups count:
<?php
	$response = $targetGroupsService->getTargetGroupsCount();
	checkResult($response);
	if ($response->isSuccess()) {
		echo "<br /><pre><ul><li>Result: " . $response->getResult() . "</li></ul>";
	}
?>
</li>
<?php } ?>

<?php if (isset($_POST['targetgroup_2'])) { ?>
	<li>
		GET target groups:
		<?php
		$response = $targetGroupsService->getTargetGroups(1, 10);
		checkResult($response);

		// Print all results
		if ($response->isSuccess()) {
			echo "<br /><pre><ul>";
			foreach ($response->getResult() as $targetgroup) {
				echo "<li>" . $targetgroup->toString() . "</li>";
			}
			echo "</ul></pre>";
		}
		?>
	</li>
<?php } ?>

<?php if (isset($_POST['targetgroup_3'])) { ?>
	<li>
		GET targetgroup with ID <?= $TESTDATA['targetGroupId']?>:
		<?php
		$response = $targetGroupsService->getTargetGroup($TESTDATA['targetGroupId']);
		checkResult($response);
		if ($response->isSuccess()) {
			echo "<br /><pre><ul><li>" . $response->getResult()->toString() . "</li></ul>";
		}
		?>
	</li>
<?php } ?>

<?php if (isset($_POST['targetgroup_4'])) { ?>
	<li>
		POST create targetgroup:
		<?php
		$targetGroup = new TargetGroup();
		$targetGroup->name = "test1234";
		$targetGroup->contactFilterId = 2765;
		$targetGroup->type = "regular";

		echo $targetGroup->toXMLString();
		die();

		$response = $targetGroupsService->createTargetGroup($targetGroup);
		checkResult($response);
		?>
	</li>
<?php } ?>

<?php if (isset($_POST['targetgroup_5'])) { ?>
	<li>
		DELETE target group:
		<?php


		$targetGroup = new TargetGroup();
		$targetGroup->name = "test1234";
		$targetGroup->contactFilterId = 1;
		$targetGroup->type = "regular";
		$response = $targetGroupsService->createTargetGroup($targetGroup);

		$response = $targetGroupsService->deleteTargetGroup($response->getResult());
		checkResult($response);
		?>
	</li>
<?php } ?>

</ul>
<?php } ?>



<?php
// ----------------------------------------------------------------------------------------------------------
// MAILINGS
// ----------------------------------------------------------------------------------------------------------

if (containsPostNeedle("mailing")) {?>

<h2>Mailings - Tests</h2>

<?php
	$mailingService = new MailingsService($config);
	$mailingService->setDebug($debug);
?>

<ul>
<?php if (isset($_POST['mailings_1'])) { ?>
<li>
GET get HTML of mailing with ID  <?= $TESTDATA['mailingId']?>:
<?php	

	$response = $mailingService->getHTMLContent($TESTDATA['mailingId']);
	
	checkResult($response);
?>
</li>
<?php } if (isset($_POST['mailings_2'])) { ?>
<li>
POST set HTML of mailing with ID <?= $TESTDATA['setMailingId']?>:
<?php	

	//$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> <html>   <head>      <title>[[MAILING|SUBJECT|]]</title>	</head>   <body bgcolor="#ebebeb">      <img src="http://www.xqueue.de/system/scripts/../../tl_files/layout/logo.png" /> 	  <a href="http://www.xqueue.de">XQueue</a>   </body></html>';
	$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
 <head>
  <title>Coole FirstSPirit Betreffzeile</title>
  <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
  <style type="text/css">
         a.line				{ text-decoration: underline; }
         a.noline			{ text-decoration: none; }
         a.iline				{ text-decoration: underline; }
         a.iline:hover		{ text-decoration: none; }
         a.inoline			{ text-decoration: none; }
         a.inoline:hover		{ text-decoration: underline; }
      </style>
 </head>
 <body style="margin:0; padding:0;" alink="#004f23" bgcolor="#dddddd" link="#004f23" text="#5a5a5a" vlink="#004f23">
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
   <tbody>
    <tr>
     <td bgcolor="#dddddd" width="100%">
      <table border="0" cellpadding="0" cellspacing="0">
       <tbody>
        <tr>
         <td style="font-size:1px;line-height:1px;" height="10"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="10" width="1"></td>
        </tr>
       </tbody>
      </table>
      <table id="otrack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td style="font-size:1px;line-height:1px;" height="1"><img alt="" style="display: none;" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" border="0" height="1" width="1"></td>
        </tr>
       </tbody>
      </table> <a name="top"></a>
      <!--[[CONTEXT-DRIVEN|MASK|ONLINE|ONLINE_ARCHIVE|PUBLIC]]-->
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td align="right" width="600"> <font style="font-size:11px;" color="#5a5a5a" size="2" face="Arial, Helvetica, Sans-Serif"> Sie haben Darstellungsprobleme? Dann klicken Sie bitte <a href="[[ONLINE-VERSION]]" target="_blank">hier</a>. </font> </td>
        </tr>
       </tbody>
      </table>
      <table id="otrack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td style="font-size:1px;line-height:1px;" height="10"><img alt="" style="display: none;" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" border="0" height="1" width="1"></td>
        </tr>
       </tbody>
      </table>
      <!--[[/CONTEXT-DRIVEN]]-->
      <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td align="right" width="600"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6245/ORIGINAL/nl_logo.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="51" width="200"></td>
        </tr>
       </tbody>
      </table>
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td valign="top" width="600"><img src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6247/ORIGINAL/nl_ticker_2016_06_header.jpg/login.ticket=1s9lxwkxqrhh" alt="" style="display:block;" border="0" height="271" hspace="0" vspace="0" width="600"></td>
        </tr>
       </tbody>
      </table>
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td style="font-size:1px;line-height:1px;" align="right" bgcolor="#ffffff"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="15" width="1"></td>
        </tr>
       </tbody>
      </table>
      <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td width="14"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="1" width="14"></td>
         <td align="left" valign="top">
          <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
           <tbody>
            <tr>
             <td align="left" valign="top">
              <table align="left" border="0" cellpadding="0" cellspacing="0">
               <tbody>
                <tr>
                 <td> <img data-fs-transfer-constraint="allow=MS_MED68IA&amp;mpicture=true&amp;txt=false&amp;hide=CS_D68ATASET&amp;stype=MED68IA&amp;mfile=false&amp;etype=&amp;up=false&amp;multi=false" data-fs-attrs="eyJsYW5ndWFnZUFiYnJldmlhdGlvbiI6IkRFIiwicmVsZWFzZSI6ZmFsc2UsImNoYW5uZWwiOjIxfQ==" data-fs-editor-type="FS_REFERENCE" data-fs-id="eyJub2RlcyI6WyJzdF9pbWFnZSJdLCJpZCI6NzcyMSwic3RvcmUiOiJQQUdFU1RPUkUifQ==" data-fs-type="GADGET" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6250/ORIGINAL/nl_ticker_2016_06_nadelbrett.jpg/login.ticket=1s9lxwkxqrhh" width="184"> </td>
                </tr>
                <tr>
                 <td height="15"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" height="15" width="1"></td>
                </tr>
               </tbody>
              </table> </td>
             <td width="10"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="1" width="10"></td>
             <td align="left" valign="top"> <font data-fs-transfer-constraint="allow=&amp;mpicture=false&amp;txt=true&amp;hide=&amp;stype=&amp;mfile=false&amp;etype=&amp;up=false&amp;multi=false" data-fs-attrs="eyJsYW5ndWFnZUFiYnJldmlhdGlvbiI6IkRFIiwicmVsZWFzZSI6ZmFsc2UsImNoYW5uZWwiOjIxfQ==" data-fs-editor-type="CMS_INPUT_TEXT" data-fs-transfer-types="2243933507" data-fs-id="eyJub2RlcyI6WyJzdF9oZWFkbGluZSJdLCJpZCI6NzcyMSwic3RvcmUiOiJQQUdFU1RPUkUifQ==" data-fs-type="GADGET" style="font-size:11px;" color="#636362" size="2" face="Arial, Helvetica, Sans-Serif"><b>„The Floating Piers“ – wie test Nadeln Brücken schlagen</b></font>
              <div data-fs-transfer-constraint="allow=&amp;mpicture=false&amp;txt=true&amp;hide=&amp;stype=&amp;mfile=false&amp;etype=&amp;up=false&amp;multi=false" data-fs-attrs="eyJsYW5ndWFnZUFiYnJldmlhdGlvbiI6IkRFIiwicmVsZWFzZSI6ZmFsc2UsImNoYW5uZWwiOjIxfQ==" data-fs-editor-type="CMS_INPUT_DOM" data-fs-transfer-types="2243933507" data-fs-id="eyJub2RlcyI6WyJzdF9jb250ZW50Il0sImlkIjo3NzIxLCJzdG9yZSI6IlBBR0VTVE9SRSJ9" data-fs-type="GADGET">
               <p class="section"><font style="font-size:11px;" color="#5a5a5a" size="2" face="Arial, Helvetica, Sans-Serif">Die Verhüllung des Reichstages in Berlin, der Pont Neuf in Paris und der Vorhang im Tal der Rocky Mountains sind nur einige von vielen Projekten, die den Künstler Christo in das Bewusstsein der Menschen gebracht haben.</font></p>
               <p></p>
               <p class="section"><font style="font-size:11px;" color="#5a5a5a" size="2" face="Arial, Helvetica, Sans-Serif">Aktuell arbeitet der Künstler wieder an einem „Art in Nature“ Großprojekt. Hierbei soll vom 13. Juni bis 3. Juli 2016 am Lago D’Iseo in Italien ein drei Kilometer langer, begehbarer Pier entstehen, der unter anderem aus 70.000 m² eines technischen vernadelten Vliesstoffes besteht. </font></p>
               <p></p>
               <p class="section"><font style="font-size:11px;" color="#5a5a5a" size="2" face="Arial, Helvetica, Sans-Serif">Als Partner für sein Projekt wählte Christo den Gronauer Vliesstoffhersteller Altex, der sich bereits durch sein Recyclingkonzept für das Gewebe, das zur Verhüllung des Reichstages eingesetzt wurde, als zuverlässiger und verlässlicher Partner etablierte. </font></p>
               <p></p>
               <p class="section"><font style="font-size:11px;" color="#5a5a5a" size="2" face="Arial, Helvetica, Sans-Serif">Ebenso zuverlässige und verlässliche Partner sind Produkte aus dem Hause test die Altex bei der Bewältigung dieser immensen Aufgabe unterstützen. <br>test drückt dem Projektteam bei der Umsetzung dieses spannenden Vorhabens die Daumen.</font></p>
               <p></p>
               <p class="section"><font style="font-size:11px;" color="#5a5a5a" size="2" face="Arial, Helvetica, Sans-Serif">Alle Information und viele Bilder zum Kunstprojekt finden Sie unter: <br>http://www.thefloatingpiers.com </font></p>
               <p></p>
              </div> </td>
            </tr>
           </tbody>
          </table> </td>
         <td width="14"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="1" width="14"></td>
        </tr>
       </tbody>
      </table>
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td style="font-size:1px;line-height:1px;" align="right" bgcolor="#ffffff"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="15" width="1"></td>
        </tr>
       </tbody>
      </table>
      <table border="0" cellpadding="0" cellspacing="0">
       <tbody>
        <tr>
         <td style="font-size:1px;line-height:1px;" height="7"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="7" width="1"></td>
        </tr>
       </tbody>
      </table>
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td style="font-size:1px;line-height:1px;" align="right" bgcolor="#ffffff"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6249/ORIGINAL/nl_claim.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="50" width="397"></td>
        </tr>
       </tbody>
      </table>
      <table border="0" cellpadding="0" cellspacing="0">
       <tbody>
        <tr>
         <td style="font-size:1px;line-height:1px;" height="14"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="14" width="1"></td>
        </tr>
       </tbody>
      </table>
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td width="14"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="1" width="14"></td>
         <td align="left" bgcolor="#dddddd" valign="top"><font style="font-size:11px;" color="#636362" size="2" face="Arial, Helvetica, Sans-Serif"> <b>Der test Online-Newsletter - Abonnement, Datenschutz und Feedback</b> <br> <br> Diese E-Mail wurde an [[CONTACT|EMAIL]] gesendet. test sendet E-Mails immer nur nach ausdrücklicher Einwilligung des Empfängers.<br> <br> Hinweise zum Thema Datenschutz und zur Verwendung Ihrer Daten finden Sie unter den <a href="https://www.test.com/cms/de/datenschutz?msgID=438230696&amp;adr=[[CONTACT|EMAIL]]" style="color: #636362" target="_blank" class="iline"><font color="#636362">Datenschutz-Richtlinien</font></a>.<br> Um Ihre individuellen Einstellungen zu ändern oder den Online-Newsletter abzubestellen, klicken Sie bitte <a href="http://news.test.com/pages/de_change_profile.php5?msgID=438230696&amp;adr=[[CONTACT|EMAIL]]" style="color: #636362" target="_blank" class="iline"><font color="#636362">hier</font></a>.<br> Ihr persönliches <a href="http://news.test.com/pages/de_feedback.php5?msgID=438230696&amp;adr=[[CONTACT|EMAIL]]" style="color: #636362" target="_blank" class="iline"><font color="#636362">Feedback</font></a> nimmt test gerne entgegen.<br> <br> </font> </td>
         <td width="14"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="1" width="14"></td>
        </tr>
       </tbody>
      </table>
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td width="14"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="1" width="14"></td>
         <td align="left" bgcolor="#dddddd" valign="top"><font style="font-size:11px;" color="#636362" size="1" face="Arial, Helvetica, Sans-Serif"> test KG | Postfach 10 02 49 | 72423 Albstadt | Germany<br> Telefon +49 7431 10-0 | Fax +49 7431 10-27 77 | <a href="mailto:contact@test.com?msgID=438230696&amp;adr=[[CONTACT|EMAIL]]" style="color: #636362" target="_blank" class="iline"><font color="#636362">contact@test.com</font></a> | <a href="http://www.test.com/?msgID=438230696&amp;adr=lukas.sadurski@xqueue.de" style="color: #636362" target="_blank" class="iline"><font color="#636362">www.test.com</font></a> <br> <br> Registrierter Firmensitz: Albstadt | Amtsgericht Stuttgart | HRA 401300<br> Umsatzsteueridentifikationsnummer: DE 144836167<br> </font> </td>
         <td width="14"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="1" width="14"></td>
        </tr>
       </tbody>
      </table>
      <table id="social" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
       <tbody>
        <tr>
         <td width="10"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="10" width="10"></td>
         <td width="580">
          <table border="0" cellpadding="0" cellspacing="0">
           <tbody>
            <tr>
             <td style="font-size:1px;line-height:1px;" height="10"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="10" width="1"></td>
            </tr>
           </tbody>
          </table>
          <div>
           <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
             <tr>
             </tr>
            </tbody>
           </table>
          </div>
          <table border="0" cellpadding="0" cellspacing="0">
           <tbody>
            <tr>
             <td style="font-size:1px;line-height:1px;" height="10"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="10" width="1"></td>
            </tr>
           </tbody>
          </table> </td>
         <td width="10"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="10" width="10"></td>
        </tr>
       </tbody>
      </table> </td>
    </tr>
   </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0">
   <tbody>
    <tr>
     <td style="font-size:1px;line-height:1px;" height="20"><img alt="" src="http://dev-shop1000.maileon.com:8000/fs5preview/preview/14/media/DE/current/6254/ORIGINAL/nl_spacer.gif/login.ticket=1s9lxwkxqrhh" style="display:block;" border="0" height="20" width="1"></td>
    </tr>
   </tbody>
  </table>
 </body>
</html>';

//	$response = $mailingService->setHTMLContent($TESTDATA['setMailingId'], '<html/>', false, false);
	//$response = $mailingService->setHTMLContent($TESTDATA['setMailingId'], $html, true, true);

	$response = $mailingService->getTextContent($TESTDATA['setMailingId']);

	$response = $mailingService->setTextContent($TESTDATA['setMailingId'], $response->getBodyData()."\r\rnew line test?");


	checkResult($response);
?>
</li>
<?php } if (isset($_POST['mailings_3'])) { ?>
<li>
GET get last 10 mailings:
<?php	

	// Check till end of day to make sure not to miss anything
	$now = date('Y-m-d+');
	
	$fields = array(MailingFields::$STATE,
					MailingFields::$TYPE, 
					MailingFields::$NAME,
					MailingFields::$SCHEDULE_TIME);


	//$response = $mailingService->getMailingsBySchedulingTime($now."00:00:00", false, $fields, 1, 10);
	$response = $mailingService->getMailingsBySchedulingTime("2015-12-01+00:00:00", false, $fields, 1, 20, "schedule_time", "DESC");
	//$response = $mailingService->getMailingsByTypes(array("trigger"), $fields, 1, 10);

    echo "<br /><hr />";
    var_dump($response->getResponseHeaders());
    echo "<br /><hr /><br />";

	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $mailing) {
			echo "<li>" . $mailing->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
</li>
<?php } if (isset($_POST['mailings_4_1'])) { ?>
<li>
GET get sender alias:
<?php	
$response = $mailingService->getSenderAlias($TESTDATA['mailingId']);
checkResult($response);

echo "<br />Result: " . $response->getResult();
?>
</li>
<?php } if (isset($_POST['mailings_4'])) { ?>
<li>
GET get sender:
<?php	
$response = $mailingService->getSender($TESTDATA['mailingId']);
checkResult($response);

echo "<br />Result: " . $response->getResult();
?>
</li>
<?php } if (isset($_POST['mailings_5'])) { ?>
<li>
POST set sender:
<?php	
$response = $mailingService->setSender($TESTDATA['mailingId'], "maxX@api.testing.news-mailer.eu");
checkResult($response);
?>
</li>
<?php } if (isset($_POST['mailings_6'])) { ?>
<li>
GET get reply-to address:
<?php
$response = $mailingService->getReplyToAddress($TESTDATA['mailingId']);
checkResult($response);
?>
</li>
<?php } if (isset($_POST['mailings_7'])) { ?>
    <li>
        POST set reply-to address:
        <?php
        $response = $mailingService->setReplyToAddress($TESTDATA['mailingId'], false, "wee@gmx.de");
        checkResult($response);
        ?>
    </li>
<?php } if (isset($_POST['mailings_8'])) { ?>
    <li>
        POST create regular mailing:
        <?php
		$name= "APITest".generateRandomString();
        $subject = "test subject";
        $response = $mailingService->createMailing($name . "_regular", $subject, true, "regular");
        checkResult($response);

		$mailingId = $response->getResult();

		//$mailingService->setHTMLContent($mailingId, "<html><head></head><body><a href=\"http://google.com\">Hello World!</a><a href=\"http://google.com/1\">Hello World!</a><a href=\"http://google.com/2\">Hello World!</a><a href=\"http://google.com/3\">Hello World!</a><a href=\"http://google.com\">Hello World!</a><a href=\"http://google.com/1\">Hello World!</a><a href=\"http://google.com/2\">Hello World!</a><a href=\"http://google.com/3\">Hello World!</a></body></html>", true, true);
		//$html = file_get_contents("C:\\Users\\mstaender\\Desktop\\test.html");
		//$mailingService->setHTMLContent($mailingId, $html, true, false);

		$mailingService->setTextContent($mailingId, "Hallo Welt sdgjkljztjfgkhjztztzjfz dth edrtjetzj tzjtzj tzfghdfghdfgh dfgh d");

		$mailingService->setSender($mailingId, "xqueue");
		$mailingService->setTargetGroupId($mailingId, 28); // the ID of the target group

		$response = $mailingService->sendMailingNow($mailingId);

		checkResult($response);

        ?>
    </li>
<?php } if (isset($_POST['mailings_9'])) { ?>
    <li>
        POST create trigger mailing:
        <?php
        $name= "APITest".generateRandomString();
        $subject = "test subject";
        $response = $mailingService->createMailing($name . "_trigger", $subject, true, "trigger");
        checkResult($response);

		$mailingId = $response->getResult();

		$response = $mailingService->setHTMLContent($mailingId, '<html/>', false, false);
		checkResult($response);

//		$response = $mailingService->setSender($mailingId, "max.mustermann");
		//$response = $mailingService->setSender($mailingId, "[[TRANSACTION|sender]]");
		$response = $mailingService->setSender($mailingId, "[[ % transaction 'sender']]");
		checkResult($response);

		$response = $mailingService->getSender($mailingId);
		checkResult($response);

		$response = $mailingService->setSenderAlias($mailingId, "[[TRANSACTION|senderAlias]]");
		checkResult($response);

		$response = $mailingService->setTriggerDispatchLogic($mailingId, "<?xml version=\"1.0\"?><dispatch_options><type>SINGLE</type><event>139</event><speed_level>low</speed_level><start_trigger>true</start_trigger></dispatch_options>");
		checkResult($response);

		$response = $mailingService->getTriggerDispatchLogic($mailingId);
		checkResult($response);

		//$response = $mailingService->deleteActiveTriggerMailing($mailingId);
		//checkResult($response);
		?>
    </li>
<?php } if (isset($_POST['mailings_10'])) { ?>
    <li>
        POST create doi mailing:
        <?php
		$name= "APITest".generateRandomString();
        $subject = "test subject";
        $response = $mailingService->createMailing($name . "_doi", $subject, true, "doi");
        checkResult($response);

        $mailingId = $response->getResult();
        $response = $mailingService->setHTMLContent($mailingId, '<html/>', false, false);
        checkResult($response);

        $response = $mailingService->setSender($mailingId, "max.mustermann");
        checkResult($response);

        $response = $mailingService->getDoiMailingKey($mailingId);
        checkResult($response);

        $response = $mailingService->setDoiMailingKey($mailingId, (string)$mailingId);
        checkResult($response);

        $response = $mailingService->getDoiMailingKey($mailingId);
        checkResult($response);

		$response = $mailingService->getTriggerDispatchLogic($mailingId);
		checkResult($response);

        $response = $mailingService->setTriggerActive($mailingId);
        checkResult($response);
        ?>
    </li>
</li><?php } if (isset($_POST['mailings_11'])) { ?>
	<li>
		PUT set dispatching logic:
		<?php
		/*
		$response = $mailingService->setTriggerDispatchLogic(483, "<?xml version=\"1.0\"?><dispatch_options><type>SINGLE</type><event>1628</event><speed_level>low</speed_level><start_trigger>true</start_trigger></dispatch_options>");
		checkResult($response);
        */
                $response = $mailingService->setTriggerDispatchLogic(1097, "<?xml version=\"1.0\"?><dispatch_options><type>MULTI</type><target>EVENT</target><event>1628</event><interval>HOUR</interval><minutes>30</minutes></dispatch_options>");
                checkResult($response);
/*
                $response = $mailingService->setTriggerDispatchLogic(460, "<?xml version=\"1.0\"?><dispatch_options><type>MULTI</type><target>CONTACTFILTER</target><contact_filter_id>18</contact_filter_id><interval>MONTH</interval><day_of_month>15</day_of_month><hours>12</hours><minutes>30</minutes></dispatch_options>");
                checkResult($response);

                // 0 ist Montag
                $response = $mailingService->setTriggerDispatchLogic(460, "<?xml version=\"1.0\"?><dispatch_options><type>MULTI</type><target>CONTACTFILTER</target><contact_filter_id>18</contact_filter_id><interval>WEEK</interval><day_of_week>0</day_of_week><hours>12</hours><minutes>30</minutes></dispatch_options>");
                checkResult($response);
        */
		?>
	</li>
<?php } if (isset($_POST['mailings_12'])) { ?>
    <li>
        DELETE delete mailing:
        <?php
        $response = $mailingService->deleteMailing(1108);
        checkResult($response);
        ?>
    </li>
<?php } if (isset($_POST['mailings_13'])) { ?>
	<li>
		DELETE deactivate trigger mailing:
		<?php
		$response = $mailingService->deactivateTriggerMailing(1108);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_14'])) { ?>
	<li>
		GET dispatch scheduling for trigger mailing:
		<?php
		$response = $mailingService->getTriggerDispatchLogic(492);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_15'])) { ?>
	<li>
		POST Copy Mailing:
		<?php
		$response = $mailingService->copyMailing(13815);
		checkResult($response);
        //echo "<hr/><br />Response Headers: ";
        //var_dump($response->getResponseHeaders());
        //echo "<hr/><br />Response Object: ";
        //var_dump($response->getResult());

        //echo phpversion();
		?>
	</li>
<?php } if (isset($_POST['mailings_15_1'])) { ?>
	<li>
		PUT Schedule Regular Mailing:
		<?php
		$response = $mailingService->setMailingSchedule(13899, "2019-01-10", 14, 01);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_15_2'])) { ?>
	<li>
		POST Update the Schedule for Regular Mailing:
		<?php
		$response = $mailingService->updateMailingSchedule(13899, "2019-03-20", 14, 01);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_16'])) { ?>
	<li>
		PUT disable all qos checks:
		<?php
		$response = $mailingService->disableQosChecks(405);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_17'])) { ?>
	<li>
		GET get archive url:
		<?php
		$response = $mailingService->getArchiveUrl(1599);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_18'])) { ?>
	<li>
		POST and GET tags:
		<?php
		$randomTags = array(generateRandomString(5), generateRandomString(5));

		echo "Setting tags: " . join("#", $randomTags) . "<br />";

		// Set some tags
		$response = $mailingService->setTags(476, $randomTags);
		checkResult($response);

		// Get them and validate that they are equal
		$response = $mailingService->getTags(476);
		checkResult($response);

		echo "<br /><b>Received tags:</b> <br />";
		echo "<ul>";
		foreach ($response->getResult() as $tag) {
			echo "<li>" . $tag . "</li>";
		}
		echo "</ul>";
		?>
	</li>
<?php } if (isset($_POST['mailings_19'])) { ?>
	<li>
		POST and GET locale:
		<?php

		$mailingId = 476;

		echo "Mailing $mailingId: Setting locale 1: de<br />";

		$response = $mailingService->setLocale($mailingId, "de");
		checkResult($response);

		$response = $mailingService->getLocale($mailingId);
		checkResult($response);

		echo "<br /><br /><b>Received locale 1: " . $response->getResult() . "</b> <br /><br /><hr /><br />";

		// Run 2, change locale
		echo "Mailing $mailingId: Setting locale 2: en<br />";

		$response = $mailingService->setLocale($mailingId, "en");
		checkResult($response);

		$response = $mailingService->getLocale($mailingId);
		checkResult($response);

		echo "<br /><br /><b>Received locale 2: " . $response->getResult() . "</b> <br />";

		?>
	</li>
<?php } if (isset($_POST['mailings_20'])) { ?>
	<li>
		POST fill RSS SmartMailing Tags:
		<?php

		$mailingId = 476;

		$response = $mailingService->fillRssSmartContentTags($mailingId);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_21'])) { ?>
	<li>
		GET check existence of mailing and getting mailing ID:
		<?php
		$randomName = generateRandomString(10);

		// Mailing should not exist
		$exists = $mailingService->checkIfMailingExistsByName($randomName);
		echo "<br />Mailing exists: " . (($exists === true)?"true":"false");

		// Create it and remember the ID
		$response = $mailingService->createMailing($randomName, $randomName);
		$mailingIdCreated = $response->getResult();
		checkResult($response);

		// Now it should exist
		$exists = $mailingService->checkIfMailingExistsByName($randomName);
		echo "<br />Mailing exists after creation: " . (($exists === true)?"true":"false");

		// Get the ID and compare
		$response = $mailingService->getMailingIdByName($randomName);
		$mailingIdRetrieved = $response->getResult();
		checkResult($response);

		// Now delete it
		$response = $mailingService->deleteMailing($mailingIdCreated);
		checkResult($response);

		echo "<br /><h4>Mailings have equel ID: ".(($mailingIdCreated === $mailingIdRetrieved)?"true":"false")."</h4>";
		?>
	</li>
<?php } if (isset($_POST['mailings_20'])) { ?>
	<li>
		POST fill RSS SmartMailing Tags:
		<?php

		$mailingId = 476;

		$response = $mailingService->fillRssSmartContentTags($mailingId);
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_21'])) { ?>
	<li>
		NOT IMPLEMENTED
		<?php
			// TBD
		?>
	</li>
	<?php } if (isset($_POST['mailings_22'])) { ?>
	<li>
		GET customProperties for mailing:
		<?php

		$mailingId = 522;

		$response = $mailingService->getCustomProperties($mailingId);
		checkResult($response);

		echo "<br /><b>Received custom properties:</b> <br />";
		echo "<ul>";
		foreach ($response->getResult() as $property) {
			echo "<li>" . $property->toString() . "</li>";
		}
		echo "</ul>";
		?>
	</li>
	<?php } if (isset($_POST['mailings_23'])) { ?>
	<li>
		POST add customProperty for mailing:
		<?php

		$mailingId = 522;

		$properties = array(
			new CustomProperty("testprop1","testval1"),
			new CustomProperty("testprop2","testval2")
		);

		$response = $mailingService->addCustomProperties($mailingId, $properties);
		checkResult($response);

		// Add single property
		$response = $mailingService->addCustomProperties($mailingId, new CustomProperty("testprop3","testval3"));
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_24'])) { ?>
	<li>
		PUT update customProperty for mailing:
		<?php

		$mailingId = 522;

		$response = $mailingService->updateCustomProperty($mailingId, new CustomProperty("testprop2","testval2.1"));
		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['mailings_25'])) { ?>
    <li>
        DELETE delete customProperty for mailing:
        <?php

        $mailingId = 522;

        $response = $mailingService->deleteCustomProperty($mailingId, "testprop2");
        checkResult($response);
        ?>
    </li>
    <?php } if (isset($_POST['mailings_26'])) { ?>
        <li>
            POST send test mail to single contact for mailing:
            <?php

            $mailingId = 13815;

            $response = $mailingService->sendTestMail($mailingId, "max.mustermann@xqueue.com");
            checkResult($response);
            ?>
        </li>
    <?php } if (isset($_POST['mailings_27'])) { ?>
        <li>
            POST send test mail to test group for mailing:
            <?php

            $mailingId = 13815;

            $response = $mailingService->sendTestMailToTestTargetGroup($mailingId, 82);
            checkResult($response);
            ?>
        </li>
    <?php } if (isset($_POST['mailings_28'])) { ?>
        <li>
            GET report URL for mailing:
            <?php

            $mailingId = 14124;

            $response = $mailingService->getReportUrl($mailingId);
            checkResult($response);

            echo "<br />Result: " . $response->getResult();
            ?>
        </li>
<?php } if (isset($_POST['mailings_29'])) { ?>
    <li>
        GET mailing template:
        <?php

        $mailingId = 14124;

        $response = $mailingService->getTemplate($mailingId);
        checkResult($response);

        echo "<br />Result: " . $response->getResult();
        ?>
    </li>
    <?php } if (isset($_POST['mailings_29'])) { // TBD: new ID ?>
        <li>
            GET mailing template:
            <?php

            $mailingId = 14124;

            $response = $mailingService->setTemplate($mailingId, 'Template name');
            checkResult($response);

            echo "<br />Result: " . $response->getResult();
            ?>
        </li>
    <?php } if (isset($_POST['mailings_30'])) { ?>
        <li>
            GET mailing preview text:
            <?php

            $mailingId = 567;

            $response = $mailingService->getPreviewText($mailingId);
            checkResult($response);

            echo "<br />Result: " . $response->getResult();
            ?>
        </li>
    <?php } if (isset($_POST['mailings_31'])) { ?>
        <li>
            SET mailing preview text:
            <?php

            $mailingId = 567;

            $response = $mailingService->setPreviewText($mailingId, "New Preview text");
            checkResult($response);
            ?>
        </li>
    <?php } if (isset($_POST['mailings_32'])) { ?>
        <li>
            GET mailing subject:
            <?php

            $mailingId = 466;

            $response = $mailingService->getSubject($mailingId);
            checkResult($response);
            
            echo "<br />Result: " . $response->getResult();
            ?>
        </li>
<?php } ?>
</ul>
<?php } // End?>

<?php
// ----------------------------------------------------------------------------------------------------------
// Reports
// ----------------------------------------------------------------------------------------------------------

if (containsPostNeedle("media_")) {
?>

    <h2>Reportings - Tests</h2>
    
    <?php
    	$mediaService = new MediaService($config);
    ?>
    <ul>
    
    <?php if (isset($_POST['media_1'])) { ?>
        <li>
        	GET list of mailing templates:
        	
        	<?php
        	$response = $mediaService->getMailingTemplates();
        	
        	if ($response->isSuccess()) {
        	    echo "<br /><pre><ul>";
        	    foreach ($response->getResult() as $template) {
        	        echo "<li>" . $template->name;
            	        echo "<ul><li>";
            	        print_r($template);
            	        echo "</li></ul>";
        	        echo "</li>";
        	    }
        	    echo "</ul></pre>";
        	}
        	?>
        </li>
        
    <?php
    }
    ?>
    </ul>
    <?php 
}

?>




<?php
// ----------------------------------------------------------------------------------------------------------
// Reports
// ----------------------------------------------------------------------------------------------------------

if (containsPostNeedle("reports_")) {
?>

<h2>Reportings - Tests</h2>

<?php
	$reportsService = new ReportsService($config); 
	$reportsService->setDebug($debug);
?>
<ul>

<?php if (isset($_POST['reports_1'])) { ?>
<li>
GET unsubscribers:
<?php

    $fromDate = "1384516564000";
    $toDate = "1384516588000";
    $mailingIds = null;
    $contactIds = null; //array($TESTDATA['userExternalId']);
    $contactEmails = null;

	$response = $reportsService->getUnsubscribers($fromDate, $toDate, $mailingIds, $contactIds, $contactEmails);
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $unsubscriber) {
			echo "<li>" . $unsubscriber->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
<br />

GET Count unsubscribers:
<?php
    $fromDate = null;
    $toDate = null;
    $mailingIds = null;
    $contactIds = null;//array("hgs");//$TESTDATA['userExternalId']);
    $contactEmails = array("hgs");

	$response = $reportsService->getUnsubscribersCount($fromDate, $toDate, $mailingIds, $contactIds, $contactEmails);
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned unsubscriber count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>
<?php } ?>


<?php if (isset($_POST['reports_2'])) { ?>
<li>
GET subscribers:
<?php
	$fromDate = strtotime('12-02-2016').'000';
	$toDate = strtotime('13-02-2016').'000';
	$mailingIds = null;
	$contactIds = null;
	$contactEmails = null;

	$response = $reportsService->getSubscribers($fromDate, $toDate, $mailingIds, $contactIds, $contactEmails, null, false, array("TITLE"), array("Dorig"), false, $TESTDATA['page_index'], $TESTDATA['page_size']);
	checkResult($response);

	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $subscriber) {
			echo "<li>" . $subscriber->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
<br />

GET Count subscribers:
<?php
	$fromDate = strtotime('12-02-2016').'000';
	$toDate = strtotime('13-02-2016').'000';
	$response = $reportsService->getSubscribersCount($fromDate, $toDate);
	checkResult($response);

	// Print all results
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned subscriber count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>

GET Count subscribers for month:
<?php

	// Start date
	$date = '2016-01-01';

	// End date
	$end_date = '2016-02-22';

	echo "<ul>";
	while (strtotime($date) <= strtotime($end_date)) {
		echo "<li>$date - Neuanmelder: ";
		$fromDate = strtotime($date).'000';
		$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
		$toDate = strtotime($date).'000';
		$response = $reportsService->getSubscribersCount($fromDate, $toDate);
		if ($response->isSuccess()) {
			echo $response->getResult();
		}
		echo '</li>';
	}
	echo "</ul>";
?>
</li>
<?php } ?>



<?php if (isset($_POST['reports_3'])) { ?>
<li>
GET recipients:
<?php
    $from = "1427714747000";
    $to = null;//"1427714818000";
	$response = $reportsService->getRecipients($from, $to, null, null, null, null, true, array("TITLE"), array("abär"), false, $TESTDATA['page_index'], $TESTDATA['page_size']);
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $subscriber) {
			echo "<li>" . $subscriber->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
<br />

GET Count recipients:
<?php
//"2014-07-30",  "2014-07-30", null, null, null, null, false
	//$response = $reportsService->getRecipientsCount(strtotime("2014-07-30"), strtotime("2014-07-31"));
	//$response = $reportsService->getRecipientsCount("1441584000000", "1441670400000");
	//$response = $reportsService->getRecipientsCount(null, null, array("141"));

    $response = $reportsService->getRecipientsCount(strtotime("2015-09-07")."000", strtotime("2015-09-08")."000");
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned recipients count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>
<?php } ?>


<?php if (isset($_POST['reports_4'])) { ?>
<li>
GET opens:
<?php
    //$fromDate = (time()-(60*60)) . "000";
    //$mailingIds = array(400); // array($TESTDATA['mailingId'])
    //$contactIds = array();

	$fromDate = null;
	$toDate = null;
	$mailingIds = null;
	$contactIds = null;
	$contactEmails = null;
	$contactExternalIds = null;
	$formatFilter = null;
	$socialNetworkFilter = null;
	$deviceTypeFilter = null;
	$embedEmailClientInfos = false;
	$excludeAnonymousOpens = true;
	$standardFields = null;
	$customFields = null;
	$embedFieldBackups = false;
	$pageIndex = $TESTDATA['page_index'];
	$pageSize = $TESTDATA['page_size'];


	$response = $reportsService->getOpens($fromDate, $toDate, $mailingIds, $contactIds, $contactEmails, $contactExternalIds, $formatFilter, $socialNetworkFilter, $deviceTypeFilter, $embedEmailClientInfos, $excludeAnonymousOpens, $standardFields, $customFields, $embedFieldBackups, $pageIndex, $pageSize);
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $opener) {
			echo "<li>" . $opener->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
<br />

GET Count opens:
<?php
	$response = $reportsService->getOpensCount(null, null, array($TESTDATA['mailingId']));
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned opens count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>
<?php } ?>


<?php if (isset($_POST['reports_5'])) { ?>
<li>
GET unique opens:
<?php

    $mailingIds = array($TESTDATA['mailingId']);
    $mailingIds = null;

    $embedEmailClientInfos = false;
    $excludeAnonymousOpens = true;

	$response = $reportsService->getUniqueOpens(null, null, $mailingIds, null, null, null, $embedEmailClientInfos, $excludeAnonymousOpens, null, null, false, $TESTDATA['page_index'], $TESTDATA['page_size']);
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $opener) {
			echo "<li>" . $opener->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
<br />

GET Count unique opens:
<?php
	$response = $reportsService->getUniqueOpensCount(null, null, array($TESTDATA['mailingId']));
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned unique opens count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>
<?php } ?>


<?php if (isset($_POST['reports_6'])) { ?>
<li>
GET clicks:
<?php


    $fromDate = null;//(time()-(60*60)) . "000";
    $mailingIds = array(366); // array($TESTDATA['mailingId'])
    $contactIds = array();

	$response = $reportsService->getClicks($fromDate, null, $mailingIds, $contactIds, null, null, null, null, null, null, null, null, false, false, array("TITLE"), array("Dorig"), false, $TESTDATA['page_index'], $TESTDATA['page_size'], true);
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $subscriber) {
			echo "<li>" . $subscriber->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
<br />

GET Count clicks:
<?php
	$response = $reportsService->getClicksCount(null, null, array($TESTDATA['mailingId']));
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned clicks count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>
<?php } ?>


<?php if (isset($_POST['reports_7'])) { ?>
<li>
GET unique clicks:
<?php
    $embedEmailClientInfos = true;
	$response = $reportsService->getUniqueClicks(null, null, array($TESTDATA['mailingId']), null, null, null, $embedEmailClientInfos, false, array("TITLE"), array("Dorig"), false, $TESTDATA['page_index'], $TESTDATA['page_size']);
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $subscriber) {
			echo "<li>" . $subscriber->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
</li>
<br />

GET Count unique clicks:
<?php
	$response = $reportsService->getUniqueClicksCount(null, null, array($TESTDATA['mailingId']));
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned unique clicks count: ' . $response->getResult() . '</li></ul>';
	}
?>
<?php } ?>


<?php if (isset($_POST['reports_8'])) { ?>
<li>
GET bounces:
<?php
	$mailingIds = array(189, 188);
	$emails = null;//array('b2b-admin@nrw-tourismus.de');
	$excludeAnonymousBounces = true;

	$response = $reportsService->getBounces(null, null, $mailingIds, null, $emails, null, null, null, null, $excludeAnonymousBounces, array(), array(), false, $TESTDATA['page_index'], $TESTDATA['page_size']);
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $subscriber) {
			echo "<li>" . $subscriber->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
<br />

GET Count bounces:
<?php
	$response = $reportsService->getBouncesCount();
	checkResult($response);
	
	// Print all results
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned bounces count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>
<?php } ?>

<?php if (isset($_POST['reports_9'])) { ?>
<li>
GET unique bounces:
<?php

    $excludeAnonymousBounces = false;

	$response = $reportsService->getUniqueBounces(null, null, null, null, null, null, $excludeAnonymousBounces, array("TITLE"), array("Dorig"), false, $TESTDATA['page_index'], $TESTDATA['page_size']);
	checkResult($response);

	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $subscriber) {
			echo "<li>" . $subscriber->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
<br />

GET Count unique bounces:
<?php
	$response = $reportsService->getUniqueBouncesCount();
	checkResult($response);

	// Print all results
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned unique bounces count: ' . $response->getResult() . '</li></ul>';
	}
?>
</li>
<?php } ?>

<?php if (isset($_POST['reports_10'])) { ?>
    <li>
        GET blocks:
        <?php
        $fromDate = null;//1444829843000 - 1 * 24 * 60 * 60 * 1000;
        $toDate = null;//1405092837000;
        $contactIds = null;
        $contactEmails = array();
        $contactExternalIds = null;
        $reasons = null;// array("blacklist");
        $oldStatus = null;
        $newStatus = null;//array("allowed");
        $excludeAnonymousBlocks = true;
        $standardFields = array("TITLE");
        $customFields = null;
        $pageIndex = $TESTDATA['page_index'];
        $pageSize = $TESTDATA['page_size'];

        $response = $reportsService->getBlocks($fromDate, $toDate, $contactIds, $contactEmails, $contactExternalIds,
            $reasons, $oldStatus, $newStatus, $excludeAnonymousBlocks, $standardFields, $customFields, $pageIndex, $pageSize);
        checkResult($response);

        // Print all results
        if ($response->isSuccess()) {
            echo "<br /><pre><ul>";
            foreach ($response->getResult() as $blocks) {
                echo "<li>" . $blocks->toString() . "</li>";
            }
            echo "</ul></pre>";
        }
        ?>
        <br />



        GET Count blocks:
        <?php
        $fromDate = 1394186104000;
        $toDate = 1405092837000;
        $contactIds = null;
        $contactEmails = null;
        $contactExternalIds = null;
        $reasons = array("blacklist");
        $oldStatus = null;
        $newStatus = array("blocked");
        $excludeAnonymousBlocks = true;

        $response = $reportsService->getBlocksCount($fromDate, $toDate, $contactIds, $contactEmails, $contactExternalIds, $reasons, $oldStatus, $newStatus, $excludeAnonymousBlocks);
        checkResult($response);

        // Print all results
        if ($response->isSuccess()) {
            echo '<br /><pre><ul><li>Returned blocks count: ' . $response->getResult() . '</li></ul>';
        }
        ?>
    </li>
<?php } ?>

<?php if (isset($_POST['reports_11'])) { ?>
    <li>
        GET conversions:
        <?php
        $fromDate = null;//1444829843000 - 1 * 24 * 60 * 60 * 1000;
        $toDate = null;//1405092837000;
        $contactIds = null;
        $mailingIds = array();
        $contactEmails = array();
        $contactExternalIds = null;
        $pageIndex = $TESTDATA['page_index'];
        $pageSize = $TESTDATA['page_size'];

        $response = $reportsService->getConversions($fromDate, $toDate, $mailingIds, $contactIds, $contactEmails, $contactExternalIds, $pageIndex, $pageSize);
        checkResult($response);

        // Print all results
        if ($response->isSuccess()) {
            echo "<br /><pre><ul>";
            foreach ($response->getResult() as $conversion) {
                echo "<li>" . $conversion->toString() . "</li>";
            }
            echo "</ul></pre>";
        }
        ?>
        <br />
    </li>
<?php } ?>

<?php if (isset($_POST['reports_12'])) { ?>
    <li>
        GET unique conversions:
        <?php
        $fromDate = null;//1444829843000 - 1 * 24 * 60 * 60 * 1000;
        $toDate = null;//1405092837000;
        $contactIds = null;
        $mailingIds = array();
        $contactEmails = array();
        $contactExternalIds = null;
        $pageIndex = $TESTDATA['page_index'];
        $pageSize = $TESTDATA['page_size'];

        $response = $reportsService->getUniqueConversions($fromDate, $toDate, $mailingIds, $contactIds, $contactEmails, $contactExternalIds, $pageIndex, $pageSize);
        checkResult($response);

        // Print all results
        if ($response->isSuccess()) {
            echo "<br /><pre><ul>";
            foreach ($response->getResult() as $conversion) {
                echo "<li>" . $conversion->toString() . "</li>";
            }
            echo "</ul></pre>";
        }
        ?>
        <br />
    </li>
<?php } ?>

</ul>
<?php } // End?>



<?php
// ----------------------------------------------------------------------------------------------------------
// Transactions
// ----------------------------------------------------------------------------------------------------------

if (containsPostNeedle("transactions_")) {
?>


    <h2>Transactions - Tests</h2>

<?php
	$transactionsService = new TransactionsService($config);
	$transactionsService->setDebug($debug);
?>

<ul>
<?php if (isset($_POST['transactions_1'])) { ?>
<li>
GET get transactions types count
<?php
        $response = $transactionsService->getTransactionTypesCount();

	checkResult($response);
	if ($response->isSuccess()) {
		echo '<br /><pre><ul><li>Returned transaction types count: ' . $response->getResult() . '</li></ul>';
	}

?>
</li>
<?php } if (isset($_POST['transactions_2'])) { ?>
<li>
GET get a list of transaction types
<?php
	$response = $transactionsService->getTransactionTypes($TESTDATA['page_index'], $TESTDATA['page_size']);
	checkResult($response);
	//print_r($response->getResult());
	// Print all results
	if ($response->isSuccess()) {
		echo "<br /><pre><ul>";
		foreach ($response->getResult() as $transactionType) {
			echo "<li>" . $transactionType->toString() . "</li>";
		}
		echo "</ul></pre>";
	}
?>
</li>
<?php } if (isset($_POST['transactions_3'])) { ?>
<li>
GET transaction type
<?php
	$response = $transactionsService->getTransactionType($TESTDATA['transactionTypeId']);
	checkResult($response);

	if ($response->isSuccess()) {
            echo "<br /><pre>" . $response->getResult()->toString() . "</ul></pre>";
	}
?>
</li>
<?php } if (isset($_POST['transactions_4'])) { ?>
	<li>
		POST create transaction type
		<?php
		$attribute_1 = new AttributeType(null, "date", DataType::$TIMESTAMP, true);

		$attributes = array($attribute_1);
		$trt = new TransactionType(null, "TestType1", $attributes, null, true);

		echo $trt->toXMLString();

		//$response = $transactionsService->createTransactionType($trt);
		//checkResult($response);

		// Get the ID
		//$id = $response->getResult();

		// Delete it again
		//$response = $transactionsService->deleteTransactionType($id);
		//checkResult($response);

		?>
	</li>
<?php } if (isset($_POST['transactions_4_1'])) { ?>
	<li>
		POST create complex transaction type
		<?php
		$attributes = array();
//		array_push($attributes, new AttributeType(null, "gender", DataType::$STRING, false));
//		array_push($attributes, new AttributeType(null, "title", DataType::$STRING, false));
//		array_push($attributes, new AttributeType(null, "first_name", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "last_name", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "phone", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "webshop_id", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "phone_contact", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "mail_contact", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "language", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "b2c", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "aktion", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "customer_no", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "order_no", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "currency", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "billing_address.name", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "billing_address.street1", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "billing_address.street2", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "billing_address.zip", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "billing_address.city", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "shipping_address.name", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "shipping_address.street1", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "shipping_address.street2", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "shipping_address.zip", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "shipping_address.city", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "items", DataType::$JSON, true));
//		array_push($attributes, new AttributeType(null, "order_date", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "total", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "payment_type", DataType::$STRING, true));
//		array_push($attributes, new AttributeType(null, "voucher_code", DataType::$STRING, false));
//		array_push($attributes, new AttributeType(null, "incentive.type", DataType::$STRING, false));
//		array_push($attributes, new AttributeType(null, "incentive.value", DataType::$STRING, false));
//		array_push($attributes, new AttributeType(null, "incentive.code", DataType::$STRING, false));
//		array_push($attributes, new AttributeType(null, "incentive.validity", DataType::$STRING, false));


		// Reminder Mails
		array_push($attributes, new AttributeType(null, "customer.gender", DataType::$STRING, false));
		array_push($attributes, new AttributeType(null, "customer.fullname", DataType::$STRING, false));
		array_push($attributes, new AttributeType(null, "customer.firstname", DataType::$STRING, false));
		array_push($attributes, new AttributeType(null, "customer.lastname", DataType::$STRING, false));
		array_push($attributes, new AttributeType(null, "customer.number", DataType::$STRING, false));
		array_push($attributes, new AttributeType(null, "reminder.unsubscribe_link", DataType::$STRING, false));
		array_push($attributes, new AttributeType(null, "reminder.reorder_all_products", DataType::$STRING, false));
		array_push($attributes, new AttributeType(null, "reminder.items", DataType::$JSON, false));
		$trt = new TransactionType(null, "reorder_reminder", $attributes);

		$response = $transactionsService->createTransactionType($trt);

		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['transactions_4_2'])) { ?>
	<li>
		POST create transaction type with archival duration
		<?php
		$attributes = array();
		$trt = new TransactionType(null, "ArchivalDurationApiTest", $attributes, 7);

		$response = $transactionsService->createTransactionType($trt);

		checkResult($response);
		?>
	</li>
<?php } if (isset($_POST['transactions_5'])) { ?>
<li>
DELETE transaction type
<?php
    $response = $transactionsService->deleteTransactionType($TESTDATA['transactionTypeId']);
	
    checkResult($response);
?>
</li>
<?php } if (isset($_POST['transactions_6'])) { ?>
	<li>
		POST create transactions
		<?php
		/*
		$transaction = new Transaction();
		$transaction->contact = new ContactReference();
		$transaction->contact->email = $TESTDATA['transactionUserEmail'];
		$transaction->type = 4;

		$transaction->content['anrede'] = "Max";
		$transaction->content['linkProfilAktualisieren'] = "http://univativ.de/profileUpdate";
		$transaction->content['linkNewsletterAbmeldung'] = "http://univativ.de/abmeldung";
		$transaction->content['ausgabe'] = "12.12.2012";

		$transaction->content['jobs'] = array(
			array(
				'id' => '000000001',
				'einsatzort' => 'Testeinsatzort 1',
				'umfang' => "Testumfang 1",
				'start' => "1.1.2001",
				'laufzeit' => "20",
				'link_mehr' => "http://univativ.de/job/000000001/mehr",
				'link_bewerben' => "http://univativ.de/job/000000001/bewerben"),
			array(
				'id' => '000000002',
				'einsatzort' => 'Testeinsatzort 2',
				'umfang' => "Testumfang 2",
				'start' => "2.2.2002",
				'laufzeit' => "20",
				'link_mehr' => "http://univativ.de/job/000000002/mehr",
				'link_bewerben' => "http://univativ.de/job/000000002/bewerben"));

		echo "<br />";
		echo json_encode( array($transaction));

		// TEST
		$transaction = new Transaction();
		$transaction->contact = new ContactReference();
		$transaction->contact->email = $TESTDATA['transactionUserEmail'];
		$transaction->type = 160;

		$transaction->content['name'] = "Max";
		$transaction->content['total'] = 12.5;
		// END TEST
*/

		/*
		// TEST
		$transaction = new Transaction();
		$transaction->contact = new ContactReference();
		$transaction->contact->email = "max.mustermann@xqueue.com";
		$transaction->type = 4;

		$transaction->content['information'] = "http://msdevelopment.org/cart100";
		$transaction->content['cart.products'] = array(
			array(
				'link' => 'http://msdevelopment.org/p1',
				'name' => 'Testname 1',
				'image' => 'http://msdevelopment.org/image1.jpg',
				'discount' => "10%"),
			array(
				'link' => 'http://msdevelopment.org/p2',
				'name' => 'Testname 2',
				'image' => 'http://msdevelopment.org/image2.jpg',
				'discount' => "20%"));
		// END TEST
	*/


		// TEST
		$transaction = new Transaction();
		$transaction->contact = new ContactReference();
		$transaction->contact->email = "max.mustermann@xqueue.com";
		$transaction->type = 245;

		$transaction->content['html'] = "<img src=\"http:\/\/flirtpiraten.com\/teaser\/email.gif?trk=1au\" width=\"1\" height=\"1\" alt=\"\">";
		// END TEST

		echo json_encode( $transaction);

		//$transactions = array($transaction1, $transaction2);
		$transactions = array($transaction);

		$response = $transactionsService->createTransactions($transactions, false, false);
		checkResult($response);

		if ($response->isSuccess() && $response->getResult()->reports[0]) {
			echo "<br /><pre>Sample for accessing message of report [0]: " . $response->getResult()->reports[0]->message . "</pre>";
		}
		?>
	</li>
<?php } if (isset($_POST['transactions_6_1'])) { ?>
	<li>
		POST create complex transactions
		<?php
		$transaction = new Transaction();
		$transaction->contact = new ContactReference();
		$transaction->contact->email = $TESTDATA['transactionUserEmail'];
		$transaction->type = $TESTDATA['transactionTypeId'];
		$transaction->type = 235; //1715;
		//
		//		$transaction->content['order'] = array('items' => array(
		//			array('title' => 'test1', 'content' => "This is some content 1."),
		//			array('title' => 'test2', 'content' => "This is some content 2.")));
		//		$transaction->content['name'] = "This is some content";

		$transaction->content['order.id'] = "76439965";
		$transaction->content['customer.full_name'] = "Max Mustermann";
		$transaction->content['order.currency'] = "€";

		$transaction->content['order.date'] = "11.11.2015";
		$transaction->content['order.total'] = 151.99;

		$transaction->content['billing'] = array(
			"fullname" => "BillingMax BillingMustermann",
			"address.line1" => "BillingStreet Line 1",
			"address.line2" => "BillingStreet Line 2",
			"address.line3" => "BillingPLZ",
			"address.line4" => "BillingCity",
			"method.type" => "Credit Card");

		$transaction->content['shipping'] = array(
			"fullname" => "ShippingMustermann",
			"address.line1" => "ShippingStreetLine1",
			"address.line2" => "ShippingStreetLine2",
			"address.line3" => "ShippingPLZ",
			"address.line4" => "ShippingCity");

		$transaction->content['order.items'] = array(
			array(
				'title' => 'Ordered Item 1',
				'articlenumber' => "0000000000001",
				'imageUrl' => "http://www.xqueue.de/tl_files/layout/logo.png",
				'unitPrice' => "20",
				'amount' => "5",
				'packagingUnit' => "Pieces",
				'cummulativePrice' => "100",
				'discountSource' => "Volume Discount",
				'deliveryTime' => "3 Working Days"),
			array(
				'title' => 'Ordered Item 2',
				'articlenumber' => "0000000000002",
				'imageUrl' => "http://www.xqueue.de/tl_files/layout/logo.png",
				'unitPrice' => "10",
				'amount' => "5",
				'packagingUnit' => "Pieces",
				'cummulativePrice' => "50",
				'discountSource' => "",
				'deliveryTime' => "3 Werktage"),
			array(
				'title' => 'Ordered Item 3',
				'articlenumber' => "0000000000003",
				'imageUrl' => "http://www.xqueue.de/tl_files/layout/logo.png",
				'unitPrice' => "1,99",
				'amount' => "1",
				'packagingUnit' => "Pieces",
				'cummulativePrice' => "1,99",
				'discountSource' => "",
				'deliveryTime' => "3 Working Days"));


		echo "<br />";
		echo json_encode( array($transaction));


		//$transactions = array($transaction1, $transaction2);
		$transactions = array($transaction);

		$response = $transactionsService->createTransactions($transactions, true, false);
		checkResult($response);

		if ($response->isSuccess() && $response->getResult()->reports[0]) {
			echo "<br /><pre>Sample for accessing message of report [0]: " . $response->getResult()->reports[0]->message . "</pre>";
		}
		?>
	</li>
<?php } if (isset($_POST['transactions_6_2'])) { ?>
	<li>
		POST create 10 transactions
		<?php
		$transaction = new Transaction();
		$transaction->contact = new ContactReference();
		$transaction->contact->email = "max.mustermann@baunzt.de";
		$transaction->type = 247;

		for ($i=0; $i<10; $i++) {
			$transaction->content['url'] = "https://hosting.maileon.com/service/tmp/response.php?email=anouar.haha$i@xqueue.com";
			$transaction->content['text'] = "$i";

//			echo "<br />";
//			echo json_encode( array($transaction));

			$transactions = array($transaction);

			echo json_encode($transactions).PHP_EOL;

			$response = $transactionsService->createTransactions($transactions, true, false);
			checkResult($response);

			if ($response->isSuccess() && $response->getResult()->reports[0]) {
				echo "<br /><pre>Sample for accessing message of report [0]: " . $response->getResult()->reports[0]->message . "</pre>";
			}
		}


		?>
	</li>
<?php } if (isset($_POST['transactions_6_3'])) { ?>
	<li>
		POST create transaction by name
		<?php
		$transaction = new Transaction();
		$transaction->contact = new ContactReference();
		$transaction->contact->email = "max.mustermann@baunzt.de";
		$transaction->typeName = "Bestellbestätigung_TEST1";
		
		// Some sample content
		$transaction->content['transaction_id'] = "123äöü45";

		$transactions = array($transaction);

		echo json_encode($transactions).PHP_EOL;

		$response = $transactionsService->createTransactions($transactions, true, false);
		checkResult($response);

		if ($response->isSuccess() && $response->getResult()->reports[0]) {
		    echo "<br /><pre>Sample for accessing message of report [0]: " . ((property_exists($response->getResult()->reports[0], 'message'))?$response->getResult()->reports[0]->message:"none") . "</pre>";
		}


		?>
	</li>
<?php } if (isset($_POST['transactions_7'])) { ?>
<li>
POST create transactions and non existing contact
<?php
    $transaction = new Transaction();
    $transaction->import = new ImportReference();
    $transaction->import->contact = new ImportContactReference();
    $transaction->import->contact->email = $TESTDATA['transactionUserEmail'];
    $transaction->import->contact->external_id = "ztfhtfg";
	$transaction->import->contact->permission = Permission::$SOI->getCode();
    $transaction->type = $TESTDATA['transactionTypeId'];
    
    $transaction->content['total'] = 12;
    $transaction->content['name'] = "This is some content";
    
    echo "<br />";
    echo json_encode( array($transaction));


    //$transactions = array($transaction1, $transaction2);
    $transactions = array($transaction);
	
	$response = $transactionsService->createTransactions($transactions, true, false);	
	checkResult($response);

	if ($response->isSuccess() && is_array($response->getResult()->reports)) {
        echo "<br /><pre>Sample for accessing message of report [0]: " . $response->getResult()->reports[0]->message . "</pre>";
	} else if ($response->isSuccess() && !is_array($response->getResult()->reports)) {
        echo "<br /><pre>Sample for accessing queued state of single report: " . $response->getResult()->reports->queued . "</pre>";
    }
?>
</li>
        <?php }
        if (isset($_POST['transactions_8'])) { ?>
            <li>
                POST create transaction with attachments
                <?php
                $transaction = new Transaction();
                $transaction->contact = new ContactReference();
                $transaction->contact->email = $TESTDATA['transactionUserEmail'];
                $transaction->type = $TESTDATA['transactionTypeId'];
                $transaction->content = array(
                    //'date' => "20.10.2013 12:00:00",
                    'url' => "http://xqueue.com",
                    'text' => "Ist der Anhang korrekt?"
                );
                $transaction->addAttachmentFromFile("awesome.png", "image/png");
				$transaction->addAttachmentFromFile("awesome.pdf", "application/pdf");
				$transaction->addAttachmentFromFile("testcard.jpg", "image/png");

                echo "<br />";
//                echo json_encode(array($transaction));


                //$transactions = array($transaction1, $transaction2);
                $transactions = array($transaction);

                $response = $transactionsService->createTransactions($transactions, true, false);
                checkResult($response);
                ?>
            </li>
        <?php }
        if (isset($_POST['transactions_9'])) { ?>
            <li>
                DELETE transactions before a given date
                <?php
                $response = $transactionsService->deleteTransactions($TESTDATA['transactionTypeId'], mktime(15, 30, 0, 4, 3, 2014));
                checkResult($response);

                if ($response->isSuccess()) {
                    echo "<br /><pre>" . $response->getResult() . "</ul></pre>";
                }
                ?>
            </li>
        <?php }
		if (isset($_POST['transactions_10'])) { ?>
		<li>
			GET recent transactions
			<?php
			$response = $transactionsService->getRecentTransactions($TESTDATA['transactionTypeId'], 1, 99);
			checkResult($response);

			echo '<ul>';
			if ($response->isSuccess()) {
				foreach ($response->getResult() as $entry) {
					echo '<li>' . $entry->toString() . '</li>\n';
				}
			}
			echo '</ul>';
			?>
		</li>
		<?php } ?>
</ul>
<?php } // End?>

<?php 
if (isset($_POST['blacklists_1'])
|| isset($_POST['blacklists_2'])
|| isset($_POST['blacklists_3'])) {?>
<h2>Blacklists - Tests</h2>

<?php
	$blacklistsService = new BlacklistsService($config);
	$blacklistsService->setDebug($debug);
?>

<ul>
<?php if (isset($_POST['blacklists_1'])) { ?>
<li>
GET a list of all blacklists
<?php
        $response = $blacklistsService->getBlacklists();

		checkResult($response);
		if ($response->isSuccess()) {
			echo '<br /><pre>Returned blacklists: <ul>';
			foreach ($response->getResult() as $blacklist) {
				echo '<li>' . $blacklist->toString() . '</li>\n';
			}
			echo "</ul></pre>";
		}
?>
</li>
<?php } if (isset($_POST['blacklists_2'])) { ?>
<li>
GET a particular blacklist
<?php
		$response = $blacklistsService->getBlacklist(39602);
		checkResult($response);
		if ($response->isSuccess()) {
			echo '<br /><pre><ul><li>' . $response->getResult()->toString() . '</li></ul></pre>';
		}
?>
</li>
<?php } if (isset($_POST['blacklists_3'])) { ?>
<li>
POST add entries to blacklist
<?php
	
	$response = $blacklistsService->addEntriesToBlacklist(39602, array('someone@toblacklist.com', 'another.person@isblacklisted.com'));
	checkResult($response);
?>
</li>
<?php } ?>
<?php } ?>

<?php if (containsPostNeedle("account_")) {?>
<h2>Account - Tests</h2>

<?php
	$accountService = new AccountService($config);
    $accountService->setDebug($debug);
?>

<ul>
<?php if (isset($_POST['account_1'])) { ?>
<li>
GET a list of all account placeholders
<?php
        $response = $accountService->getAccountPlaceholders();

		checkResult($response);
		if ($response->isSuccess()) {
			echo '<br /><pre>Returned account placeholders: <ul>';
			foreach ($response->getResult() as $accountPlaceholder) {
				echo '<li>' . $accountPlaceholder->toString() . '</li>';
			}
			echo "</ul></pre>";
		}
?>
</li>
<?php } if (isset($_POST['account_2'])) { ?>
    <li>
        POST set account placeholders
        <?php

        // First, get current placeholders:
        $response = $accountService->getAccountPlaceholders();
        $placeholders = array();
        foreach ($response->getResult() as $accountPlaceholder) {
            $placeholders[] = $accountPlaceholder;
        }

        // Add new placeholder
        $placeholders[] = new AccountPlaceholder("ApiTest", "This is a test placeholder created by the API");

        $response = $accountService->setAccountPlaceholders($placeholders);
        checkResult($response);
        ?>
    </li>
<?php } if (isset($_POST['account_3'])) { ?>
    <li>
        PUT update/add account placeholders
        <?php

        // Add new placeholder
        $placeholders = array();
        $placeholders[] = new AccountPlaceholder("ApiTest 2", "This is a test placeholder created by the API (updating/adding)");

        $response = $accountService->updateAccountPlaceholders($placeholders);
        checkResult($response);
        ?>
    </li>
<?php } if (isset($_POST['account_4'])) { ?>
<li>
DELETE remove account placeholder
<?php

	$response = $accountService->deleteAccountPlaceholder("ApiTest");
	checkResult($response);
?>
</li>
<?php } ?>
</ul>
<?php } // End?>

</body>
</html>


<?php
// Some helper functions
// Generate random strings e.g. for mailing names
function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

// Just check if there is a POST-Variable with a given string in it to decide if to show some category if tests or not
function containsPostNeedle($needle) {
    foreach ($_POST as $key => $value) {
        if (strpos($key, $needle) !== false) {  // note triple = is needed because strpos could return 0 which would be false
            return true;
        }
    }
    return false;
}
?>