<?php

namespace Maileon\Test;

use PHPUnit\Framework\TestCase;
use Maileon\MaileonAPIException;

class MaileonAPIExceptionTest extends TestCase
{

    public function testGetResponse()
    {
        $exception = new MaileonAPIException("Something went wrong", "The response");
        $this->assertEquals("The response", $exception->getResponse());
    }
}
