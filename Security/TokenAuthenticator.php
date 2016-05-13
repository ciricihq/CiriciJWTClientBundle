<?php

namespace Cirici\JWTClientBundle\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Token Authenticator.
 *
 * @DI\Service("project.token.authenticator")
 */
class TokenAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        try {
            $user = $token->getUser();
            $userProvider->getUsernameForApiKey($user->getToken());
        } catch (\Exception $e) {
            // CAUTION: this message will be returned to the client
            // (so don't put any un-trusted messages / error strings here)
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }

        return new UsernamePasswordToken(
            $user,
            $user->getPassword(),
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken
            && $token->getProviderKey() === $providerKey;
    }

    /**
     * TokenAuthenticator constructor.
     *
     * @param RepositoryInterface $repository
     *
     * @DI\InjectParams({
     *   "repository" = @DI\Inject("project.repository.api"),
     * })
     */
    public function __construct(LoggerInterface $logger,  $repository)
    {
        $this->logger = $logger;
        $this->repository = $repository;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {

        try {
            if (null === $username || null === $password) {
                throw new AuthenticationException('Username and password must be defined');
            }

            $data = [
                'form_params' => [
                    '_username' => $username,
                    '_password' => $password,
                ],
            ];

            try {
                // Call here your server to get a JWT Token from username and password.
                // I Use an API Repository based on Guzzle.
                $clientResponse = $this->repository->loginCheck($data);
                $token = json_decode($clientResponse->getBody(), true);

                if (!isset($token['token'])) {
                    throw new AuthenticationException('API No Auth Token returned');
                }
                $apiKey = $token['token'];

                if (!$apiKey) {
                    throw new AuthenticationException('API No Key found');
                }

                list($username, $roles) = $this->getUsernameForApiKey($apiKey);

                $user = new ApiUser($username, $password, '', $roles, $apiKey);

                return new UsernamePasswordToken(
                    $user,
                    $password,
                    $providerKey,
                    $roles
                );
            } catch (HttpException $ex) {
                switch ($ex->getStatusCode()) {
                    case Response::HTTP_UNAUTHORIZED:
                        throw new AuthenticationException('API Unauthorized: '. $ex->getMessage());
                    case Response::HTTP_FORBIDDEN:
                        throw new AuthenticationException('API Forbidden: '. $ex->getMessage());
                }
            }
        } catch (AuthenticationException $ex) {
            $this->logger->error($ex->getMessage());
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }
    }
}
