<?php

namespace Cirici\JWTClientBundle\Security;

use Namshi\JOSE\SimpleJWS;

/**
 * JwtVerifier
 *
 * @author  genar@acs.li
 *
 * @DI\Service("project.token.jwt_verifier")
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
     *
     * @DI\InjectParams({
     *   "publicKeyPath" = @DI\Inject("%jwt_public_key_path%"),
     * })
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
        $jws = SimpleJWS::load($jwt);
        $public_key = openssl_pkey_get_public(file_get_contents($this->publicKeyPath));

        // If the JWT is valid we return the payload
        if ($jws->isValid($public_key, 'RS256')) {
            return $jws->getPayload();
        }

        throw new AuthenticationException('API Unauthorized');
    }
}
