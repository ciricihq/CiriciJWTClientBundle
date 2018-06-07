<?php

namespace Cirici\JWTClientBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class ApiUser implements \Serializable, EquatableInterface, ApiUserInterface, UserInterface
{
    private $username;
    private $password;
    private $salt;
    private $roles;
    private $token;
    private $payload;

    public function initializeUser($username, $password, $salt, $token, array $payload)
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->token = $token;
        $this->payload = $payload;
        $this->roles = isset($payload['roles']) ? $payload['roles'] : ['roles' => 'ROLE_USER'];
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * getPayload
     *
     * @access public
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function serialize()
    {
        return serialize([
            $this->token,
            $this->username,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        list (
            $this->token,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }
}
