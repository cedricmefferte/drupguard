<?php
namespace App\Registry;

class EntityRegistry
{
    private array $entityMappings = [];

    public function addEntityMapping(string $category, string $type, string $entityClass): void
    {
        if (!isset($this->entityMappings[$category])) {
            $this->entityMappings[$category] = [];
        }

        $this->entityMappings[$category][$type] = $entityClass;
    }

    public function getEntityClass(string $category, string $type): ?string
    {
        return $this->entityMappings[$category][$type] ?? null;
    }
}
