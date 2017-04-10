<?php

namespace Cirici\JWTClientBundle\Tests\Security;

use Cirici\JWTClientBundle\Security\ApiUser;

class ApiUserTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        // Test with invalid constructor parameters
        $user = new ApiUser();
        $user->initializeUser('test', '1234', '', [], []);

        $this->assertEquals('test', $user->getUsername());
    }
}
