<?php
namespace App\Registry;

class EntityFieldRegistry
{
    private array $fieldMappings = [];

    private array $entityClasses = [];

    public function __construct(array $config)
    {
        foreach ($config['fields'] as $entityClass => $fields) {
            $this->fieldMappings[$entityClass] = $fields;
        }

        foreach ($config['plugins'] as $type => $plugins) {
            foreach ($plugins as $plugin) {
                $this->entityClasses[$plugin['name']] = $plugin['entity_class'];
            }
        }
    }
    public function getFieldMappingsByType(string $type): array
    {
        $entityClass = $this->getEntityClassByType($type);
        return $this->fieldMappings[$entityClass] ?? [];
    }

    public function getEntityClassByType(string $type): ?string
    {
        return $this->entityClasses[$type] ?? null;
    }
}
