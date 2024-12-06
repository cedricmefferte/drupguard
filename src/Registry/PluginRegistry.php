<?php
namespace App\Registry;

use InvalidArgumentException;

class PluginRegistry
{
    private array $plugins = [];

    private array $configurations = [];


    public function setConfigurations($configurations) : void
    {
        $this->configurations = $configurations;
    }

    public function getConfiguration($attribute) : array
    {
        if ($this->hasConfiguration($attribute)) {
            return $this->configurations[$attribute];
        }
        return [];
    }

    public function hasConfiguration($attribute) : bool
    {
        return isset($this->configurations[$attribute]);
    }

    public function getFormClass(string $type): ?string
    {
        return $this->plugins[$type]['form_class'] ?? null;
    }

    public function getEntityClass(string $type): ?string
    {
        return $this->plugins[$type]['entity_class'] ?? null;
    }

    public function getRepositoryClass(string $type): ?string
    {
        return $this->plugins[$type]['repository_class'] ?? null;
    }

    public function getPluginTypes(string $type): ?array
    {
        return $this->plugins[$type]['plugin_types'] ?? [];
    }

    public function getPlugins(string $type): array
    {
        return $this->configurations[$type] ?? [];
    }

    public function getPluginByName(string $type, string $name): ?array
    {
        foreach ($this->getPlugins($type) as $plugin) {
            if ($plugin['name'] === $name) {
                return $plugin;
            }
        }
        return null;
    }

    public function getPluginFields(string $type, string $name): array
    {
        $plugin = $this->getPluginByName($type, $name);
        return $plugin['fields'] ?? [];
    }

    public function getMainPluginKeys(): array
    {
        // TODO: Name Constant for qualifying plugin_types as specific node (plugin sub types conf collection )
        return array_filter(
            array_keys($this->configurations),
            fn($key) => $key !== 'plugin_types'
        );
    }

}
