<?php

namespace Cirici\JWTClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class CiriciJWTClientExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($config['use_external_jwt_api'] === true) {
            $loader->load('token_external_authenticator.yml');
        }

        // convert settings to parameters in order to acces them from controllers
        $container->setParameter('jwt_public_key_path', $config['public_key_path']);

        // convert settings to parameters in order to acces them from controllers
        $container->setParameter('jwt_token_path', $config['jwt_token_path']);
        $container->setParameter('api_user_class', $config['api_user_class']);
    }
}
