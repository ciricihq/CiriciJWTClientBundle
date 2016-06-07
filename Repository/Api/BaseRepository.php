<?php

namespace Cirici\JWTClientBundle\Repository\Api;

// use AppBundle\Repository\RepositoryInterface;
use AppBundle\Security\ApiUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use JMS\DiExtraBundle\Annotation as DI;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class BaseRepository.
 */
class BaseRepository
{
    /**
     * @var
     */
    protected $client;

    /**
     * @var TokenStorageInterface
     */
    protected $securityTokenStorage;

    /**
     * BaseRepository constructor.
     * @param $client
     * @param TokenStorageInterface $securityTokenStorage
     */
    public function __construct($client, TokenStorageInterface $securityTokenStorage)
    {
        $this->client = $client;
        $this->securityTokenStorage = $securityTokenStorage;
    }

    /**
     * @param $url
     * @param bool $public
     * @return mixed
     */
    protected function getData($url, $public = true)
    {
        try {
            $client = $this->client->get();

            $options = [];

            $token = $this->getUserToken();
            if (null !== $token) {
                $options = array_merge_recursive(
                    $options,  [
                    'headers' => [
                        'Authorization' => sprintf('Bearer %s', $token),
                    ],
                ]);

                $url .= sprintf('?bearer=%s', $token);
            }

            return $client->get($url, $options);
        } catch (RequestException $ex) {
            $response = $ex->getResponse();
            throw new HttpException($response->getStatusCode(), $ex->getMessage().'-'.$response->getReasonPhrase());
        }
    }

    /**
     * getUserToken
     *
     * @access protected
     * @return object
     */
    protected function getUserToken()
    {
        $user = $this->securityTokenStorage->getToken()->getUser();
        if (is_object($user) && $user instanceof ApiUser) {
            return $user->getToken();
        }

        return null;
    }

    /**
     * loginCheck
     *
     * @param mixed $data
     * @access public
     * @return string
     */
    public function loginCheck($data)
    {
        try {
            $token = $this->client->post('/login_check', $data);
        } catch (RequestException $ex) {
            $response = $ex->getResponse();
            throw new HttpException($response->getStatusCode(), $ex->getMessage().'-'.$response->getReasonPhrase());
        }

        return $token;
    }
}
