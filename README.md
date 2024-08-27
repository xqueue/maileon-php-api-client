[![Latest Stable Version](http://poser.pugx.org/xqueue/maileon-api-client/v)](https://packagist.org/packages/xqueue/maileon-api-client)
[![License](http://poser.pugx.org/xqueue/maileon-api-client/license)](https://packagist.org/packages/xqueue/maileon-api-client)
[![PHP Version Require](http://poser.pugx.org/xqueue/maileon-api-client/require/php)](https://packagist.org/packages/xqueue/maileon-api-client)

# Maileon API Client

Provides an API client to connect to XQueue Maileon's REST API and (de-)serializes all API functions and data for easier use in PHP projects.

Maileon's REST API documentation can be found [here](https://maileon.com/support/rest-api-1-0/).

## Table of contents
 * [Requirements](#requirements)
 * [Installation](#installation)
 * [Usage](#usage)
 * [Examples](#examples)


The API client requires `PHP >= 7.0` with `libxml` and `libcurl`.

Additionally all requests use an SSL encrypted API endpoint.
To enable SSL support in CURL, please follow these steps:
 1. Download the official SSL cert bundle by CURL from https://curl.haxx.se/ca/cacert.pem
 2. Save the bundle to a directory that can be accessed by your PHP installation
 3. Add the following entry to your php.ini (remember to change the path to where you put the cert bundle):
```
curl.cainfo="your-path-to-the-bundle/cacert.pem"
```


## Installation

You can add this library to your project using [Composer](https://getcomposer.org/):

```
composer require xqueue/maileon-api-client
```

## Usage

The API client divides the features of Maileon's REST API into specific consumable services. Each service provides all functions of it's specific category.

The following services are available:

* **Contacts**
Read, subscribe, edit, unsubscribe or delete contacts. Functions for individual contacts or bulk requests if required.

* **Blacklists**
Manage your blacklists.

* **Contactfilters**
Segmentate your address pool by filter rules.

* **Targetgroups**
Manage distribution lists to specify who gets which mailing.

* **Reports**
Get all [KPI](https://kpi.org/KPI-Basics) information about your mailings and general reportings about your contact pool.

* **Mailings**
Manage and control your mailings.

* **Transactions**
Manage transaction endpoints (events) or send new transactions to trigger sendouts or Marketing Automation programs.

* **Marketing Automations**
Start your predefined Marketing Automation programs.

* **Accounts**
Configure specific account features.

* **Medias**
Manage mailing templates.

* **Webhooks**
Manage automatic data distributions to notify external systems of specific events.


## Examples

### Contact examples

* Request basic **contact data** identified by their email address: 
```php
<?php

use de\xqueue\maileon\api\client\contacts\ContactsService;

require __DIR__ . '/vendor/autoload.php';

$contactsService = new ContactsService([
    'API_KEY' => 'Your API key',
]);

$contact = $contactsService->getContactByEmail('foo@bar.com')->getResult();

/**
* The contact object stores all information you requested.
* 
* Identifiers (Maileon ID, Maileon external id and email address), marketing permission
* level, creation date and last update date are always included if they are set in Maileon.
* 
* ID: $contact->id
* Email: $contact->email
* Permission: $contact->permission->getType()
*/
```

* Request a contact identified by it's email address including their first name and a predefined custom field and also check for a valid response:
```php
<?php

use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\StandardContactField;

require __DIR__ . '/vendor/autoload.php';

$contactsService = new ContactsService([
    'API_KEY' => 'Your API key',
]);

$getContact = $contactsService->getContactByEmail(
    email:'foo@bar.com',
    standard_fields:[
        StandardContactField::$FIRSTNAME,
        StandardContactField::$LASTNAME,
    ],
    custom_fields:[
        'My custom field in Maileon',
    ]
);

if (!$getContact->isSuccess()) {
    die($getContact->getResultXML()->message);
}

$contact = $getContact->getResult();

/**
 * The contact object stores all information you requested.
 * 
 * Identifiers (Maileon ID, Maileon external id and email address), marketing permission
 * level, creation date and last update date are always included if they are set in Maileon.
 * 
 * ID: $contact->id
 * Email: $contact->email
 * Permission: $contact->permission->getType()
 * First name: $contact->standard_fields[StandardContactField::$FIRSTNAME];
 * Custom field: $contact->custom_fields['My custom field in Maileon'];
 */
```

* Create a contact in Maileon
```php
<?php

use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\Preference;
use de\xqueue\maileon\api\client\contacts\StandardContactField;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;

require __DIR__ . '/vendor/autoload.php';

$contactsService = new ContactsService([
    'API_KEY' => 'Your API key',
]);

$contact = new Contact(
    email:'foo@bar.com',
    standard_fields:[
        StandardContactField::$FIRSTNAME => 'Foo',
        StandardContactField::$LASTNAME => 'Bar',
    ],
    custom_fields:[
        'My custom field in Maileon' => 'A value corresponding to the field type',
    ],
);

$creation = $contactsService->createContact(
    contact:$contact,
    syncMode:SynchronizationMode::$IGNORE,
    src:'An optional source of the contact creation',
    subscriptionPage:'An additional source of the contact creation',
    doi:true,
    doiPlus:true, // Enable single user tracking with the DOI process
    doiMailingKey:'A key to identify the DOI mailing',
);

if (!$creation->isSuccess()) {
    die($creation->getResultXML()->message);
}
```

* Synchronize a larger list of contacts in bulk
```php
<?php

use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Contacts;
use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use de\xqueue\maileon\api\client\contacts\StandardContactField;

require __DIR__ . '/vendor/autoload.php';

$contactsService = new ContactsService([
    'API_KEY' => 'Your API key',
]);

$contactList = new Contacts();

for ($i=1; $i<=10000; $i++) {
    $contactList->addContact(
        new Contact(
            email:"foo-{$i}@bar.com",
            standard_fields:[
                StandardContactField::$FIRSTNAME => 'Foo',
                StandardContactField::$LASTNAME => 'Bar',
            ],
            custom_fields:[
                'My custom field in Maileon' => 'A value corresponding to the field type',
            ],
        )
    );
}

$response = $contactsService->synchronizeContacts(
    contacts:$contactList,
    syncMode:SynchronizationMode::$IGNORE,
    useExternalId:false,
    ignoreInvalidContacts:true,
    reimportUnsubscribedContacts:false,
    overridePermission:false,
    updateOnly:false,
);

// The response contains some statistics and, if ignore_invalid_contacts is set
// to true, information about possibly failed contact creations, see
// https://maileon.com/support/synchronize-contacts/#articleTOC_3
```

### Report example

* Print all unsubscriptions:
```php
<?php

use de\xqueue\maileon\api\client\reports\ReportsService;

require __DIR__ . '/vendor/autoload.php';

$contactsService = new ReportsService([
    'API_KEY' => 'Your API key',
]);

$index = 1;
do {
    $getUnsubscribers = $contactsService->getUnsubscribers(
        pageIndex:$index++,
        pageSize:1000
    );
    
    foreach ($getUnsubscribers->getResult() as $unsubscriber) {
        printf('%s unsusbcribed in mailing %u at %s'.PHP_EOL,
            $unsubscriber->contact->email,
            $unsubscriber->mailingId,
            $unsubscriber->timestamp
        );
    }
} while($getUnsubscribers->getResponseHeaders()['X-Pages'] >= $index);
```

* Get [KPI](https://kpi.org/KPI-Basics) data for a specific mailing:
```php
<?php

use de\xqueue\maileon\api\client\reports\ReportsService;

require __DIR__ . '/vendor/autoload.php';

$reportsService = new ReportsService([
    'API_KEY' => 'Your API key',
]);

$mailingId = 123;

$recipients = $reportsService->getRecipientsCount(mailingIds:[$mailingId])->getResult();
$opens = $reportsService->getOpensCount(mailingIds:[$mailingId])->getResult();
$clicks = $reportsService->getClicksCount(mailingIds:[$mailingId])->getResult();
$unsubscribers = $reportsService->getUnsubscribersCount(mailingIds:[$mailingId])->getResult();
$conversions = $reportsService->getConversionsCount(mailingIds:[$mailingId])->getResult();
```

### Mailing example

* Create a new mailing, add custom HTML content, attach a target group and send it immediately:
```php
<?php

use de\xqueue\maileon\api\client\mailings\MailingsService;

require __DIR__ . '/vendor/autoload.php';

$mailingsService = new MailingsService([
    'API_KEY' => 'Your API key',
]);

$mailingId = $mailingsService->createMailing(
    name:'My campaign name',
    subject:'Hi [CONTACT|FIRSTNAME]! We got some news for you!'
)->getResult();

$mailingsService->setSender($mailingId, 'foo@bar.com');
$mailingsService->setSenderAlias($mailingId, 'Maileon news team');
$mailingsService->setHTMLContent(
    mailingId:$mailingId,
    html:'<html>...</html>',
    doImageGrabbing:true,
    doLinkTracking:true
);
$mailingsService->setTargetGroupId($mailingId, 123);
$mailingsService->sendMailingNow($mailingId);
```

### Transaction example

* Send a new transaction including product information as an order confirmation:
```php
<?php

use de\xqueue\maileon\api\client\transactions\ContactReference;
use de\xqueue\maileon\api\client\transactions\Transaction;
use de\xqueue\maileon\api\client\transactions\TransactionsService;

require __DIR__ . '/vendor/autoload.php';

$transactionsService = new TransactionsService([
    'API_KEY' => 'Your API key',
]);

$transaction = new Transaction(
    typeName:'My event to trigger',
    contact:new ContactReference(
        email:'foo@bar.com'
    ),
    content:[
        'foo' => 'bar',
        'items' => [
            [
                'name' => 'foo',
                'quantity' => 2,
                'price' => 27.99
            ],
            [
                'name' => 'bar',
                'quantity' => 1,
                'price' => 16.49
            ],
        ],
    ]
);

$transactionsService->createTransactions([$transaction]);
```
