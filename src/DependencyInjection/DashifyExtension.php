<?php

namespace Dashify\DashifyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DashifyExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        // Load all configuration files
        $loader->load('services.yaml');
        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('dashify.config', $config);
        
        // Set bundle directory as parameter
        $bundleDir = dirname(__DIR__);
        $container->setParameter('dashify.bundle_dir', $bundleDir);
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('twig')) {
            return;
        }

        $container->prependExtensionConfig('twig', [
            'paths' => [
                __DIR__ . '/../Resources/views' => 'Dashify'
            ]
        ]);

        // Add routing configuration
        if ($container->hasExtension('framework')) {
            $container->prependExtensionConfig('framework', [
                'router' => [
                    'resource' => '@DashifyBundle/Resources/config/routes/dashify.yaml',
                    'type' => 'yaml'
                ]
            ]);
        }
    }

    public function getAlias(): string
    {
        return 'dashify';
    }
} 