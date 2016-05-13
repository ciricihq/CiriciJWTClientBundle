<?php

namespace AppBundle\Repository\Api;

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
 *
 * @DI\Service("project.repository.api")
 */
class BaseRepository
{
    /**
     * @var
     */
    protected $client;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TokenStorageInterface
     */
    protected $securityTokenStorage;

    /**
     * BaseRepository constructor.
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     * @param $client
     * @param TokenStorageInterface $securityTokenStorage
     *
     * @DI\InjectParams({
     *    "client" = @DI\Inject("guzzle.client.api_jwt"),
     *    "securityTokenStorage" = @DI\Inject("security.token_storage"),
     * })
     */
    public function __construct(KernelInterface $kernel, LoggerInterface $logger, $client, TokenStorageInterface $securityTokenStorage)
    {
        $this->kernel = $kernel;
        $this->logger = $logger;
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
            $this->logger->debug('API call with Guzzle', ['url', $url]);
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

    protected function getUserToken()
    {
        $user = $this->securityTokenStorage->getToken()->getUser();
        if (is_object($user) && $user instanceof ApiUser) {
            return $user->getToken();
        }

        return null;
    }
}

