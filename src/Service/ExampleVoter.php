<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ExampleVoter implements VoterInterface
{
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        // TODO: Implement vote() method.
    }
}