<?php

namespace Cirici\JWTClientBundle\Tests\Security;

use Cirici\JWTClientBundle\Security\TokenUserProvider;
use Cirici\JWTClientBundle\Security\TokenAuthenticator;
use Cirici\JWTClientBundle\Security\JwtVerifier;

class TokenAuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    private $tokenAuthenticator;

    public function setUp()
    {
        parent::setUp();
        $tokenUserProvider = new TokenUserProvider();
        $tokenJwtVerifier = new JwtVerifier('1234');
        $this->tokenAuthenticator = new TokenAuthenticator($tokenUserProvider, $tokenJwtVerifier);
    }

    public function testCleanToken()
    {
        $token = 'Bearer 1234';
        $cleanToken = $this->tokenAuthenticator->cleanToken($token);

        $this->assertEquals('1234', $cleanToken);
    }
}
