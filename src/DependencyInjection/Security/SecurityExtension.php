<?php

namespace Dashify\DashifyBundle\DependencyInjection\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class SecurityExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new SecurityConfiguration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('dashify.security.config', $config);
        $container->setParameter('dashify.user.class', $config['user_class']);
        $container->setParameter('dashify.user.identifier_property', $config['user_identifier_property']);

        // Configuration de base pour le provider
        if ($container->hasExtension('security')) {
            $container->prependExtensionConfig('security', [
                'password_hashers' => [
                    'Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface' => 'auto'
                ],
                'providers' => [
                    'dashify_users' => [
                        'entity' => [
                            'class' => $config['user_class'],
                            'property' => $config['user_identifier_property']
                        ]
                    ]
                ]
            ]);
        }
    }

    public function getAlias(): string
    {
        return 'dashify_security';
    }
}

class SecurityConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dashify_security');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enable_login')->defaultTrue()->end()
                ->scalarNode('login_route')->defaultValue('dashify_login')->end()
                ->scalarNode('logout_route')->defaultValue('dashify_logout')->end()
                ->scalarNode('login_redirect_route')->defaultValue('dashify_dashboard')->end()
                ->scalarNode('logout_redirect_route')->defaultValue('dashify_login')->end()
                ->scalarNode('user_provider')->defaultValue('app.user_provider')->end()
                ->scalarNode('password_hasher')->defaultValue('auto')->end()
                ->scalarNode('user_class')->defaultValue('App\Entity\User')->end()
                ->scalarNode('user_identifier_property')->defaultValue('email')->end()
                ->booleanNode('remember_me')->defaultTrue()->end()
                ->integerNode('session_lifetime')->defaultValue(3600)->end()
                ->arrayNode('roles')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('view')
                            ->scalarPrototype()->end()
                            ->defaultValue(['ROLE_DASHIFY_USER'])
                        ->end()
                        ->arrayNode('create')
                            ->scalarPrototype()->end()
                            ->defaultValue(['ROLE_DASHIFY_ADMIN'])
                        ->end()
                        ->arrayNode('edit')
                            ->scalarPrototype()->end()
                            ->defaultValue(['ROLE_DASHIFY_ADMIN'])
                        ->end()
                        ->arrayNode('delete')
                            ->scalarPrototype()->end()
                            ->defaultValue(['ROLE_DASHIFY_ADMIN'])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
} 