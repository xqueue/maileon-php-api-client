<?php

namespace Maileon\Test\Src\Mailings;

use Maileon\Utils\PingService;
use PHPUnit\Framework\TestCase;

class PingServiceTest extends TestCase
{
    private static $service;

    public static function setUpBeforeClass()
    {
        self::$service = new PingService($GLOBALS['config']);
    }

    public function testGet()
    {
        $response = self::$service->pingGet();
        $this->assertTrue($response->isSuccess());
    }

    public function testPut()
    {
        $response = self::$service->pingGet();
        $this->assertTrue($response->isSuccess());
    }

    public function testDelete()
    {
        $response = self::$service->pingGet();
        $this->assertTrue($response->isSuccess());
    }

    public function testPost()
    {
        $response = self::$service->pingGet();
        $this->assertTrue($response->isSuccess());
    }
}
