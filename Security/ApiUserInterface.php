<?php

namespace Cirici\JWTClientBundle\Security;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

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
    public function isAccountNonExpired();
    public function isAccountNonLocked();
    public function isCredentialsNonExpired();
    public function isEnabled();
    public function serialize();
    public function unserialize($serialized);
}
