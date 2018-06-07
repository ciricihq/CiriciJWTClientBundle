<?php

namespace Cirici\JWTClientBundle\Security;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ApiUserInterface
{
    public function initializeUser($username, $password, $salt, $token, array $payload);
    public function getRoles();
    public function getPassword();
    public function getSalt();
    public function getUsername();
    public function getToken();
    public function getPayload();
    public function eraseCredentials();
    public function isEqualTo(UserInterface $user);
    public function serialize();
    public function unserialize($serialized);
}
