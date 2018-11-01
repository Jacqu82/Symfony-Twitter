<?php

namespace App\Tests\Security;

use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    public function testTokenGeneration()
    {
        $tokenGenerator = new TokenGenerator();
        $token = $tokenGenerator->getRandomSecureToken(30);
//        $token[15] = '*';
//        echo $token;

        $this->assertEquals(30, strlen($token));
        //$this->assertEquals(true, preg_match("/[A-Za-z0-9]/", $token));
        $this->assertTrue(ctype_alnum($token), 'Token contains incorrect characters');
    }
}