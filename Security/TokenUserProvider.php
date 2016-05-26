<?php

namespace Cirici\JWTClientBundle\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Token User Provider.
 *
 * @DI\Service("project.token.user_provider")
 */
class TokenUserProvider implements UserProviderInterface
{
    const JWT_TOKEN_PARTS_COUNT = 3;
    const TOKEN_REFRESH_DELAY = 120;

    /**
     * TokenUserProvider constructor.
     *
     * @param LoggerInterface $logger
     *
     *
     * @DI\InjectParams({
     * })
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
            $this->logger->error($ex->getMessage());
            throw new CustomUserMessageAuthenticationException('You have been disconnected, try to reconnect.');
        }
    }

    public function loadUserByUsername($username)
    {
        // NOT USED IN OUR CASE !!!
        return new ApiUser($username,  null, '', ['roles' => 'ROLE_USER'], '');
    }

    public function refreshUser(UserInterface $user)
    {

        if (!$user instanceof ApiUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        list($username, $payload) = $this->getUsernameForApiKey($user->getToken());

        return new ApiUser($username,  null, '', $user->getToken(), $payload);
    }

    public function supportsClass($class)
    {
        return 'AppBundle\Security\ApiUser' === $class;
    }
}
