<?php

namespace Cirici\JWTClientBundle\Security;

use Namshi\JOSE\SimpleJWS;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * JwtVerifier
 *
 * @author  genar@acs.li
 */
class JwtVerifier
{
    /**
     * @var string
     */
    protected $publicKeyPath;

    /**
     * __construct
     *
     * @access public
     * @return void
     */
    public function __construct($publicKeyPath)
    {
        $this->publicKeyPath = $publicKeyPath;
    }

    /**
     * verifyJWT
     *
     * @param mixed $jwt
     * @access private
     * @return void
     */
    public function verifyJWT($jwt)
    {
        try {
            $jws = SimpleJWS::load($jwt);
        } catch (InvalidArgumentException $e){
            throw new AuthenticationException('Invalid jwt token');
        }

        $public_key = openssl_pkey_get_public(file_get_contents($this->publicKeyPath));

        // If the JWT is valid we return the payload
        if ($jws->isValid($public_key, 'RS256')) {
            return $jws->getPayload();
        }

        throw new AuthenticationException('API Unauthorized');
    }
}
