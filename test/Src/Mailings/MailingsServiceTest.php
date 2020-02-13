<?php

namespace Maileon\Test\Src\Mailings;

use PHPUnit\Framework\TestCase;
use Maileon\Mailings\MailingsService;

class MailingsServiceTest extends TestCase
{
    private static $service;

    private static $TEST_MAILING_NAME = 'PHP integration test mailing';
    private static $TEST_MAILING_SUBJECT = 'Hello integration test!';
    private static $TEST_MAILING_CONTENT = '<html><body><strong>Eat more numbers more often!</strong></body></html>';

    private static $testMailingId;

    public static function setUpBeforeClass()
    {
        self::$service = new MailingsService($GLOBALS['config']);
    }

    public static function tearDownAfterClass()
    {
        $debug = self::$service->isDebug();
        self::$service->setDebug(false);
        if (self::$testMailingId) {
            self::$service->deleteMailing(self::$testMailingId);
        }
        self::$service->setDebug($debug);
    }

    public function testCreateMailing()
    {
        $response = self::$service->createMailing(
            self::$TEST_MAILING_NAME,
            self::$TEST_MAILING_SUBJECT
        );
        $this->assertTrue($response->isSuccess());
        self::$testMailingId = $response->getResult();
    }

    /**
     * @depends testCreateMailing
     */
    public function testSetHTMLContent()
    {
        $response = self::$service->setHTMLContent(self::$testMailingId, self::$TEST_MAILING_CONTENT);
        $this->assertTrue($response->isSuccess());
    }

    /**
     * @depends testSetHTMLContent
     */
    public function testGetHTMLContent()
    {
        $response = self::$service->getHTMLContent(self::$testMailingId);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(self::$TEST_MAILING_CONTENT, $response->getResult());
    }

    /**
     * @depends testGetHTMLContent
     */
    public function testDeleteMailing()
    {
        $response = self::$service->deleteMailing(self::$testMailingId);
        $this->assertTrue($response->isSuccess());
        self::$testMailingId = null;
    }
}
