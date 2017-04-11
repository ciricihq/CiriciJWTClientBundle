<?php

namespace Cirici\JWTClientBundle\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Token User Provider.
 */
class TokenUserProvider implements UserProviderInterface
{
    const JWT_TOKEN_PARTS_COUNT = 3;
    const TOKEN_REFRESH_DELAY = 120;

    /**
     * getUsernameForApiKey
     *
     * @param mixed $apiKey
     * @access public
     * @return void
     */
    public function getUsernameForApiKey($apiKey)
    {
        try {
            $tokenParts = explode('.', $apiKey);
            if (self::JWT_TOKEN_PARTS_COUNT !== count($tokenParts)) {
                throw new AuthenticationException('TOKEN Wrong Auth Token format');
            }

            $payload = json_decode(base64_decode($tokenParts[1]), true);
            if (!isset($payload['username'])) {
                throw new AuthenticationException('TOKEN No Username found in the Auth Token');
            }

            if (!isset($payload['exp'])) {
                throw new AuthenticationException('TOKEN No expiration timestamp found in the Auth Token');
            }

            $exp = $payload['exp'];
            if ($exp + (int) self::TOKEN_REFRESH_DELAY <= time()) {
                throw new AuthenticationException('TOKEN Expired');
            }

            return [
                $payload['username'],
                $payload
            ];

        } catch (\Exception $ex) {
            throw new CustomUserMessageAuthenticationException('You have been disconnected, try to reconnect.');
        }
    }

    /**
     * loadUserByUsername
     *
     * @param mixed $username
     * @access public
     * @return void
     */
    public function loadUserByUsername($username, $payload = [])
    {
        if ($payload) {
            return $this->loadUserByPayload($payload);
        }

        $user = new $this->userClass();
        $user->initializeUser($username,  null, '', ['roles' => 'ROLE_USER'], []);

        return $user;
    }

    /**
     * refreshUser
     *
     * @param UserInterface $user
     * @access public
     * @return void
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof $this->userClass) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        list($username, $payload) = $this->getUsernameForApiKey($user->getToken());

        $populatedUser = new $this->userClass();

        $populatedUser->initializeUser($username,  null, '', $user->getToken(), $payload);
        return $populatedUser;
    }

    /**
     * supportsClass
     *
     * @param mixed $class
     * @access public
     * @return void
     */
    public function supportsClass($class)
    {
        return $this->userClass === $class;
    }

    /**
     * loadUserByPayload
     *
     * @param mixed $payload
     * @access private
     * @return
     */
    private function loadUserByPayload($payload)
    {
        $user = new $this->userClass();
        return $user->initializeUser($payload['username'], null, null, null, $payload);
    }

    /**
     * setUserClass
     *
     * @param bool $userClass
     * @access public
     * @return void
     */
    public function setUserClass($userClass = '\Cirici\JWTClientBundle\Security\ApiUser')
    {
        $this->userClass = $userClass;
    }
}
