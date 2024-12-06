<?php

namespace App\DependencyInjection\Compiler;

use App\Registry\EntityRegistry;
use App\Registry\FormRegistry;
use App\Registry\PluginRegistry;
use App\Registry\RepositoryRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class PluginRegistryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
    {
        if (!$container->has(PluginRegistry::class)) {
            return;
        }

        $config = $container->getExtensionConfig('drupguard');
        $pluginsConfig = $this->findSubKey($config, 'plugins');

        $pluginRegistry = $container->findDefinition(PluginRegistry::class);
        $pluginRegistry->addMethodCall('setConfigurations', [$pluginsConfig]);

        // Vérifier la disponibilité des registres
        if (!$container->has(FormRegistry::class) ||
            !$container->has(EntityRegistry::class) ||
            !$container->has(RepositoryRegistry::class)) {
            return;
        }

        $formRegistry = $container->findDefinition(FormRegistry::class);
        $entityRegistry = $container->findDefinition(EntityRegistry::class);
        $repositoryRegistry = $container->findDefinition(RepositoryRegistry::class);

        foreach ($pluginsConfig as $mainPluginKey => $pluginConfig) {

            $typeFormClass = $pluginConfig['form_class'] ?? null;
            $typeEntityClass = $pluginConfig['entity_class'] ?? null;
            $typeRepositoryClass = $pluginConfig['repository_class'] ?? null;

            if ($typeFormClass) {
                if (!empty($pluginConfig['form_class']) && is_string($pluginConfig['form_class'])) {
                    $formRegistry->addMethodCall('addFormMapping', [$mainPluginKey, $pluginConfig['form_class']]);
                }
                $this->ensureServiceDefinition($container, $typeFormClass);
            }

            if ($typeEntityClass) {
                $entityRegistry->addMethodCall('addEntityMapping', [$mainPluginKey, $typeEntityClass]);
                $this->ensureServiceDefinition($container, $typeEntityClass);
            }

            if ($typeRepositoryClass) {
                $repositoryRegistry->addMethodCall('addRepositoryMapping', [$mainPluginKey, $typeRepositoryClass]);
                $this->ensureServiceDefinition($container, $typeRepositoryClass);
            }

            $pluginTypesConfig = $pluginConfig['plugin_types'] ?? [];
            foreach ($pluginTypesConfig as $typeName => $pluginTypeConfig) {
                $pluginName = $pluginTypeConfig['name'];
                $pluginFormClass = $pluginTypeConfig['form_class'] ?? null;
                $pluginEntityClass = $pluginTypeConfig['entity_class'] ?? null;
                $pluginRepositoryClass = $pluginTypeConfig['repository_class'] ?? null;

                if (!$pluginName || !$pluginTypeConfig) {
                    throw new \InvalidArgumentException("Each plugin must define 'name' and 'type'.");
                }

                if ($pluginFormClass) {
                    $formRegistry->addMethodCall('addFormMapping', [$pluginName, $pluginFormClass]);
                }

                if ($pluginEntityClass) {
                    $entityRegistry->addMethodCall('addEntityMapping', [$pluginName, $typeName, $pluginEntityClass]);
                }

                if ($pluginRepositoryClass) {
                    $repositoryRegistry->addMethodCall('addRepositoryMapping', [$pluginName, $typeName, $pluginRepositoryClass]);
                }
            }
        }
    }

    /**
     * Vérifie si un service est défini pour une classe donnée et l'enregistre si nécessaire.
     *
     * @param ContainerBuilder $container
     * @param string $class
     */
    private function ensureServiceDefinition(ContainerBuilder $container, string $class): void
    {
        if (!$container->has($class)) {
            $definition = new Definition($class);
            $definition->setAutowired(true)
                ->setAutoconfigured(true);
            $container->setDefinition($class, $definition);
        }
    }

    private function findSubKey(array $array, string $key, $default = [])
    {
        foreach ($array as $item) {
            if (is_array($item) && array_key_exists($key, $item)) {
                return $item[$key];
            }
        }
        return $default;
    }
}
