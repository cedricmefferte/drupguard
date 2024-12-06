<?php

namespace App\DependencyInjection;

use App\DependencyInjection\DrupguardConfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DrupguardExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new DrupguardConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('drupguard', $config);
        // Chargez les services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
        $loader->load('drupguard.yaml');

//        /*
//         *  Plugin services Aggregation
//         */
//        if (!$container->hasDefinition(
//            'app.configuration_builders'
//        )
//        ) {
//            $taggedServiceHolder = new Definition();
//            $taggedServiceHolder->setClass(
//                'App\DependencyInjection\Compiler\ConfigurationBuilderChain'
//            );
//            $container->setDefinition(
//                'app.configuration_builders',
//                $taggedServiceHolder
//            );
//        }
    }

    public function getAlias(): string
    {
        return 'drupguard';
    }
}
