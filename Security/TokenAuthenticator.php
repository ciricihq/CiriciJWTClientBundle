<?php

namespace Cirici\JWTClientBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManager;

/**
 * Token Authenticator.
 *
 * based on guard-authentication
 * ref: http://symfony.com/doc/current/cookbook/security/guard-authentication.html
 */
class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $jwtVerifier;

    private $tokenUserProvider;

    public function __construct($jwtVerifier, $tokenUserProvider)
    {
        $this->jwtVerifier = $jwtVerifier;
        $this->tokenUserProvider = $tokenUserProvider;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {
        if (!$token = $request->headers->get('Authorization')) {
            // no token? Return null and no other methods will be called
            return;
        }

        $token = $this->cleanToken($token);

        // What you return here will be passed to getUser() as $credentials
        return array(
            'token' => $token,
        );
    }

    /**
     * getUser
     *
     * Retrieves the user from a token, it must validate before
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @access public
     * @return void
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiKey = $credentials['token'];

        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        $username = $this->tokenUserProvider->getUsernameForApiKey($apiKey);
        $user = $this->tokenUserProvider->loadUserByUsername($username[0], $username[1]);
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        //
        $token = $this->cleanToken($credentials['token']);
        if ($this->jwtVerifier->verifyJWT($token)) {
            return true;
        }

        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, 403);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }

    private function cleanToken($token)
    {
        $cleanToken = str_replace('Bearer ', '', $token);
        return $cleanToken;
    }
}
