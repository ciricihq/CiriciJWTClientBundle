<?php

namespace Cirici\JWTClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cirici_jwt_client');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->scalarNode('public_key_path')
                    ->info("The path where should be loaded the public key")
                    ->defaultValue('%kernel.root_dir%/var/jwt/public.pem')
                ->end()
                ->scalarNode('jwt_token_path')
                    ->info("The path where the external API should post")
                    ->defaultValue('/jwt/token')
                ->end()
                ->booleanNode('use_external_jwt_api')
                    ->info("Load the external api token authenticator")
                    ->defaultValue(false)
                ->end()
                ->scalarNode('api_user_class')
                    ->info("If you want to use your own class you should define here")
                    ->defaultValue('\Cirici\JWTClientBundle\Security\ApiUser')
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
