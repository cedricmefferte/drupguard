<?php
namespace App\Registry;

class RepositoryRegistry
{
    private array $repositoryMappings = [];

    public function addRepositoryMapping(string $category, string $type, string $repositoryClass): void
    {
        if (!isset($this->repositoryMappings[$category])) {
            $this->repositoryMappings[$category] = [];
        }

        $this->repositoryMappings[$category][$type] = $repositoryClass;
    }

    public function getRepositoryClass(string $category, string $type): ?string
    {
        return $this->repositoryMappings[$category][$type] ?? null;
    }
}
