<?php

namespace App\DependencyInjection\Compiler;

use App\Registry\EntityRegistry;
use App\Registry\FormRegistry;
use App\Registry\RepositoryRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FieldRegistryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FormRegistry::class) ||
            !$container->has(EntityRegistry::class) ||
            !$container->has(RepositoryRegistry::class)) {
            return;
        }

        //TODO: define constants
        $pluginsConfig = $container->getParameter('drupguard')['plugins'];

        $formRegistry = $container->findDefinition(FormRegistry::class);
        $entityRegistry = $container->findDefinition(EntityRegistry::class);
        $repositoryRegistry = $container->findDefinition(RepositoryRegistry::class);

        foreach ($pluginsConfig as $plugin) {
            $name = $plugin['name'];
            $type = $plugin['type'];
            $formClass = $plugin['form_class'];
            $entityClass = $plugin['entity_class'];
            $repositoryClass = $plugin['repository_class'];

            if (empty($name) || empty($type)) {
                throw new \InvalidArgumentException("Each plugin must define 'name' and 'type'.");
            }

            if (!empty($formClass)) {
                $formRegistry->addMethodCall('addFormMapping', [$name, $type, $formClass]);

                if (!$container->has($formClass)) {
                    $definition = new Definition($formClass);
                    $definition->setAutowired(true)
                        ->setAutoconfigured(true);
                    $container->setDefinition($formClass, $definition);
                }
            }
            if (!empty($entityClass)) {
                $entityRegistry->addMethodCall('addEntityMapping', [$name, $type, $entityClass]);
                if (!$container->has($entityClass)) {
                    $definition = new Definition($entityClass);
                    $definition->setAutowired(true)
                        ->setAutoconfigured(true);
                    $container->setDefinition($entityClass, $definition);
                }
            }

            if (!empty($repositoryClass)) {
                $repositoryRegistry->addMethodCall('addRepositoryMapping', [$name, $type, $repositoryClass]);

                if (!$container->has($repositoryClass)) {
                    $definition = new Definition($repositoryClass);
                    $definition->setAutowired(true)
                        ->setAutoconfigured(true);
                    $container->setDefinition($repositoryClass, $definition);
                }
            }
        }
    }
}
