<?php

namespace Maileon\Test\Src\Contatcts;

use Maileon\Contacts\Contact;
use Maileon\Contacts\Contacts;
use Maileon\Contacts\Permission;
use Maileon\Contacts\ContactsService;
use Maileon\Contacts\SynchronizationMode;
use Maileon\Contacts\StandardContactField;
use PHPUnit\Framework\TestCase;

class ContactsServiceTest extends TestCase
{
    private static $TESTCONTACT_EMAIL = 'isabell.integrationstest@xqueue.com';
    private static $TESTCONTACT_FIRSTNAME = 'Isabell';
    private static $TESTCONTACT_FIRSTNAME_AFTER_UPDATE = 'Ingrid';
    private static $TESTCONTACT_LASTNAME = 'Integrationstest';
    private static $TESTCONTACT_EXTERNALID = 'integrationtest-id-1';
    private static $CUSTOM_FIELD_NAME = 'Dorig';
    private static $CUSTOM_FIELD_VALUE = 'true';
    private static $TESTCONTACT2_EMAIL = 'hans.wurst@xqueue.com';
    private static $TESTCONTACT2_FIRSTNAME = 'Hans';
    private static $TESTCONTACT2_LASTNAME = 'Wurst';
    private static $TESTCONTACT2_EXTERNALID = 'integrationtest-id-2';

    private static $TEST_CUSTOM_FIELD_NAME = 'Toastpaint';
    private static $TEST_CUSTOM_FIELD_NAME2 = 'Unughzanny';
    private static $TEST_CUSTOM_FIELD_TYPE = 'string';
    private static $TEST_CUSTOM_FIELD_VALUE = 'Quotimer';

    private static $service;
    private static $contactId;
    private static $contactCountBeforeCreate;

    public static function setUpBeforeClass()
    {
        self::$service = new ContactsService($GLOBALS['config']);
        $debug = self::$service->isDebug();
        self::$service->setDebug(false);
        try {
            self::$service->deleteContactByEmail(self::$TESTCONTACT_EMAIL);
        } catch (\Exception $e) {
        }
        try {
            self::$service->deleteContactByEmail(self::$TESTCONTACT2_EMAIL);
        } catch (\Exception $e) {
        }
        try {
            self::$service->deleteCustomField(self::$TEST_CUSTOM_FIELD_NAME);
        } catch (\Exception $e) {
        }
        try {
            self::$service->deleteCustomField(self::$TEST_CUSTOM_FIELD_NAME2);
        } catch (\Exception $e) {
        }
        self::$service->setDebug($debug);
    }

    public function testGetCountBeforeCreate()
    {
        $response = self::$service->getContactsCount();
        $this->assertTrue($response->isSuccess());
        self::$contactCountBeforeCreate = $response->getResult();
    }

    /**
     * @depends testGetCountBeforeCreate
     */
    public function testCreateContact()
    {
        $contact = new Contact();
        $contact->email = self::$TESTCONTACT_EMAIL;
        $contact->external_id = self::$TESTCONTACT_EXTERNALID;
        $contact->permission = Permission::$DOI_PLUS;
        $contact->standard_fields[StandardContactField::$FIRSTNAME] =
            self::$TESTCONTACT_FIRSTNAME;
        $contact->standard_fields[StandardContactField::$LASTNAME] =
            self::$TESTCONTACT_LASTNAME;
        $contact->custom_fields[self::$CUSTOM_FIELD_NAME] = self::$CUSTOM_FIELD_VALUE;
        $response = self::$service->createContact($contact, SynchronizationMode::$UPDATE);
        $this->assertTrue($response->isSuccess());
        return $contact->email;
    }

    /**
     * @depends testCreateContact
     */
    public function testGetContactsCountIncreases()
    {
        $response = self::$service->getContactsCount();
        $this->assertGreaterThan(self::$contactCountBeforeCreate, $response->getResult());
    }

    /**
     * @depends testCreateContact
     */
    public function testGetContactByEmail()
    {
        $standardFields = array(
            StandardContactField::$FIRSTNAME,
            StandardContactField::$LASTNAME
        );
        $customFields = array(self::$CUSTOM_FIELD_NAME);
        $response = self::$service->getContactByEmail(self::$TESTCONTACT_EMAIL, $standardFields, $customFields);
        $this->assertTrue($response->isSuccess());
        /* @var $contact com_maileon_api_contacts_Contact */
        $contact = $response->getResult();
        $this->assertInstanceOf('Maileon\Contacts\Contact', $contact);
        $this->assertEquals(Permission::$DOI_PLUS, $contact->permission);
        $this->assertEquals(
            $contact->standard_fields[StandardContactField::$FIRSTNAME],
            self::$TESTCONTACT_FIRSTNAME
        );
        $this->assertEquals(
            $contact->standard_fields[StandardContactField::$LASTNAME],
            self::$TESTCONTACT_LASTNAME
        );
        $this->assertEquals(self::$CUSTOM_FIELD_VALUE, $contact->custom_fields[self::$CUSTOM_FIELD_NAME]);
        $this->assertGreaterThan(0, $contact->id);
        self::$contactId = $contact->id;
    }

    /**
     * @depends testGetContactByEmail
     */
    public function testGetContact()
    {
        $standardFields = array(
            StandardContactField::$FIRSTNAME,
            StandardContactField::$LASTNAME
        );
        $customFields = array(self::$CUSTOM_FIELD_NAME);
        $response = self::$service->getContact(self::$contactId, null, $standardFields, $customFields, true);
        $this->assertTrue($response->isSuccess());
        /* @var $contact com_maileon_api_contacts_Contact */
        $contact = $response->getResult();
        $this->assertInstanceOf('Maileon\Contacts\Contact', $contact);
        $this->assertEquals(Permission::$DOI_PLUS, $contact->permission);
        $this->assertEquals(
            $contact->standard_fields[StandardContactField::$FIRSTNAME],
            self::$TESTCONTACT_FIRSTNAME
        );
        $this->assertEquals(
            $contact->standard_fields[StandardContactField::$LASTNAME],
            self::$TESTCONTACT_LASTNAME
        );
        $this->assertEquals(self::$CUSTOM_FIELD_VALUE, $contact->custom_fields[self::$CUSTOM_FIELD_NAME]);
        $this->assertGreaterThan(0, $contact->id);
        $this->contactId = $contact->id;
    }

    /**
     * @depends testCreateContact
     */
    public function testGetContacts()
    {
        $found = false;
        $numContacts = self::$service->getContactsCount()->getResult();
        for ($pageIndex = 1; !$found && ($pageIndex - 1) * 100 < $numContacts; $pageIndex++) {
            $response = self::$service->getContacts($pageIndex, 100);
            $this->assertTrue($response->isSuccess());
            $contacts = $response->getResult();
            $this->assertInstanceOf('Maileon\Contacts\Contact', $contacts);
            $found = $this->hasContactWithEmail($contacts, self::$TESTCONTACT_EMAIL);
        }
        $this->assertTrue($found);
    }

    /**
     * @depends testCreateContact
     */
    public function testGetContactsByExternalId()
    {
        $response = self::$service->getContactsByExternalId(self::$TESTCONTACT_EXTERNALID);
        $contacts = $response->getResult();
        $this->assertInstanceOf('Maileon\Contacts\Contact', $contacts);
        $this->assertTrue($this->hasContactWithEmail($contacts, self::$TESTCONTACT_EMAIL));
    }

    /**
     * @depends testCreateContact
     */
    public function testUpdateContact()
    {
        $contact = new Contact();
        $contact->id = self::$contactId;
        $contact->email = self::$TESTCONTACT_EMAIL;
        $contact->external_id = self::$TESTCONTACT_EXTERNALID;
        $contact->permission = Permission::$DOI_PLUS;
        $contact->standard_fields[StandardContactField::$FIRSTNAME] =
            self::$TESTCONTACT_FIRSTNAME_AFTER_UPDATE;
        $response = self::$service->updateContact($contact, null, null, null, false, false, true);
        $this->assertTrue($response->isSuccess());
    }

    /**
     * @depends testUpdateContact
     * @depends testGetContact
     * @depends testGetContactByEmail
     */
    public function testContactWasUpdated()
    {
        $standardFields = array(
            StandardContactField::$FIRSTNAME,
            StandardContactField::$LASTNAME
        );
        $customFields = array(self::$CUSTOM_FIELD_NAME);
        $response = self::$service->getContact(self::$contactId, null, $standardFields, $customFields, true);
        $contact = $response->getResult();
        $this->assertInstanceOf('Maileon\Contacts\Contact', $contact);
        $this->assertEquals(Permission::$DOI_PLUS, $contact->permission);
        $this->assertEquals(
            $contact->standard_fields[StandardContactField::$FIRSTNAME],
            self::$TESTCONTACT_FIRSTNAME_AFTER_UPDATE
        );
        $this->assertEquals(
            $contact->standard_fields[StandardContactField::$LASTNAME],
            self::$TESTCONTACT_LASTNAME
        );
        $this->assertEquals(self::$CUSTOM_FIELD_VALUE, $contact->custom_fields[self::$CUSTOM_FIELD_NAME]);
    }

    /**
     * @depends testContactWasUpdated
     */
    public function testSynchronizeContacts()
    {
        $contacts = new Contacts();
        $contacts->addContact(new Contact(
            null,
            self::$TESTCONTACT2_EMAIL,
            null,
            self::$TESTCONTACT2_EXTERNALID,
            null,
            array(
                StandardContactField::$LASTNAME => self::$TESTCONTACT2_LASTNAME,
                StandardContactField::$FIRSTNAME => self::$TESTCONTACT2_FIRSTNAME
            ),
            array()
        ));

        $contacts->addContact(new Contact(
            null,
            self::$TESTCONTACT_EMAIL,
            null,
            self::$TESTCONTACT_EXTERNALID,
            null,
            array(
                StandardContactField::$LASTNAME => self::$TESTCONTACT_LASTNAME,
                StandardContactField::$FIRSTNAME => self::$TESTCONTACT_FIRSTNAME
            ),
            array()
        ));

        $response = self::$service->synchronizeContacts($contacts, null, SynchronizationMode::$UPDATE);
        $this->assertTrue($response->isSuccess());
    }

    /**
     * @depends testSynchronizeContacts
     */
    public function testContactsWereSynchronized()
    {
        $standardFields = array(
            StandardContactField::$FIRSTNAME,
            StandardContactField::$LASTNAME
        );
        $customFields = array();
        $response = self::$service->getContacts(1, 999, $standardFields, $customFields);
        $contacts = $response->getResult();
        $this->assertInstanceOf('Maileon\Contacts\Contacts', $contacts);
        $contact1 = $this->findContactByEmail($contacts, self::$TESTCONTACT_EMAIL);
        $this->assertEquals(
            self::$TESTCONTACT_FIRSTNAME,
            $contact1->standard_fields[StandardContactField::$FIRSTNAME]
        );
        $contact2 = $this->findContactByEmail($contacts, self::$TESTCONTACT2_EMAIL);
        $this->assertEquals(self::$TESTCONTACT2_EXTERNALID, $contact2->external_id);
    }

    /**
     * @depends testContactsWereSynchronized
     */
    public function testUnsubscribeContactByEmail()
    {
        $response = self::$service->unsubscribeContactByEmail(self::$TESTCONTACT2_EMAIL);
        $this->assertTrue($response->isSuccess());
        $response2 = self::$service->getContacts();
        $this->assertFalse($this->hasContactWithEmail($response2->getResult(), self::$TESTCONTACT2_EMAIL));
        self::$service->deleteContactByEmail(self::$TESTCONTACT2_EMAIL);
    }

    /**
     * @depends testContactsWereSynchronized
     */
    public function testUnsubscribeContactById()
    {
        $response = self::$service->unsubscribeContactById(self::$contactId);
        $this->assertTrue($response->isSuccess());
        $response2 = self::$service->getContacts();
        $this->assertFalse($this->hasContactWithEmail($response2->getResult(), self::$TESTCONTACT_EMAIL));
        self::$service->deleteContact(self::$contactId);
    }

    /**
     * @depends testUnsubscribeContactByEmail
     * @depends testUnsubscribeContactById
     */
    public function testRecreateDeletedContacts()
    {
        $this->testSynchronizeContacts();
    }

    /**
     * @depends testRecreateDeletedContacts
     */
    public function testDeleteContactById()
    {
        $getContactResponse = self::$service->getContactByEmail(self::$TESTCONTACT_EMAIL, array(), array(), true);
        $id = $getContactResponse->getResult()->id;
        $response = self::$service->deleteContact($id);
        $this->assertTrue($response->isSuccess());
        $getContactResponse = self::$service->getContact($id, null, array(), array(), true);
        $this->assertEquals(404, $getContactResponse->getStatusCode());
    }

    /**
     * @depends testRecreateDeletedContacts
     */
    public function testDeleteContactByEmail()
    {
        $getContactResponse = self::$service->getContactByEmail(self::$TESTCONTACT2_EMAIL, array(), array(), true);
        $id = $getContactResponse->getResult()->id;
        $response = self::$service->deleteContactByEmail(self::$TESTCONTACT2_EMAIL);
        $this->assertTrue($response->isSuccess());
        $getContactResponse = self::$service->getContact($id, null, array(), array(), true);
        $this->assertEquals(404, $getContactResponse->getStatusCode());
    }

    /**
     * @depends testDeleteContactById
     */
    public function testDeleteContactByExternalId()
    {
        $this->testCreateContact();
        $getContactResponse = self::$service->getContactByEmail(self::$TESTCONTACT_EMAIL, array(), array(), true);
        $id = $getContactResponse->getResult()->id;
        $response = self::$service->deleteContactsByExternalId(self::$TESTCONTACT_EXTERNALID);
        $this->assertTrue($response->isSuccess());
        $getContactResponse = self::$service->getContact($id, null, array(), array(), true);
        $this->assertEquals(404, $getContactResponse->getStatusCode());
    }

    public function testDeleteAllContacts()
    {
        // probably not.
    }

    private function findContactByEmail($contacts, $email)
    {
        foreach ($contacts as $contact) {
            if ($contact->email == $email) {
                return $contact;
            }
        }
        $this->fail("Contact ${email} not found.");
    }

    private function hasContactWithEmail($contacts, $email)
    {
        foreach ($contacts as $contact) {
            if ($contact->email == $email) {
                return true;
            }
        }
        return false;
    }

    public function testCreateCustomField()
    {
        $response = self::$service->createCustomField(self::$TEST_CUSTOM_FIELD_NAME, self::$TEST_CUSTOM_FIELD_TYPE);
        $this->assertTrue($response->isSuccess());
    }

    /**
     * @depends testCreateCustomField
     */
    public function testGetCustomFields()
    {
        $response = self::$service->getCustomFields();
        $customFields = $response->getResult()->custom_fields;
        $this->assertArrayHasKey(self::$TEST_CUSTOM_FIELD_NAME, $customFields);
        $this->assertEquals(self::$TEST_CUSTOM_FIELD_TYPE, $customFields[self::$TEST_CUSTOM_FIELD_NAME]);
    }

    /**
     * @depends testCreateCustomField
     */
    public function testCustomFieldIsWritable()
    {
        $contact = new Contact();
        $contact->email = self::$TESTCONTACT_EMAIL;
        $contact->permission = Permission::$DOI_PLUS;
        $contact->custom_fields[self::$TEST_CUSTOM_FIELD_NAME] = self::$TEST_CUSTOM_FIELD_VALUE;
        $response = self::$service->createContact($contact, SynchronizationMode::$UPDATE);
        $this->assertTrue($response->isSuccess());
    }

    /**
     * @depends testGetCustomFields
     * @depends testCustomFieldIsWritable
     */
    public function testRenameCustomField()
    {
        $response = self::$service->renameCustomField(self::$TEST_CUSTOM_FIELD_NAME, self::$TEST_CUSTOM_FIELD_NAME2);
        $this->assertTrue($response->isSuccess());
    }

    /**
     * @depends testRenameCustomField
     */
    public function testRenamedFieldHasRightValue()
    {
        $response = self::$service->getContactByEmail(
            self::$TESTCONTACT_EMAIL,
            array(),
            array(self::$TEST_CUSTOM_FIELD_NAME2)
        );
        $this->assertEquals(
            self::$TEST_CUSTOM_FIELD_VALUE,
            $response->getResult()->custom_fields[self::$TEST_CUSTOM_FIELD_NAME2]
        );
    }

    /**
     * @depends testRenamedFieldHasRightValue
     */
    public function testDeleteCustomFieldValues()
    {
        self::$service->deleteCustomFieldValues(self::$TEST_CUSTOM_FIELD_NAME2);
        $response = self::$service->getContactByEmail(
            self::$TESTCONTACT_EMAIL,
            array(),
            array(self::$TEST_CUSTOM_FIELD_NAME2)
        );
        $this->assertArrayNotHasKey(self::$TEST_CUSTOM_FIELD_NAME2, $response->getResult()->custom_fields);
    }

    /**
     * @depends testDeleteCustomFieldValues
     */
    public function testDeleteCustomField()
    {
        self::$service->deleteCustomField(self::$TEST_CUSTOM_FIELD_NAME2);
        $response = self::$service->getCustomFields();
        $customFields = $response->getResult()->custom_fields;
        $this->assertArrayNotHasKey(self::$TEST_CUSTOM_FIELD_NAME2, $customFields);
    }
}
