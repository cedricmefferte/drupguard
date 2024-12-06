<?php
namespace App\Registry;

class FormRegistry
{
    private array $formMappings = [];

    public function addFormMapping(string $key, string $formClass): void
    {
        $this->formMappings[$key] = $formClass;
    }

    public function getFormClass(string $name): ?string
    {
        return $this->formMappings[$name] ?? null;
    }

    public function getCategoryFormClass(string $name, string $type): ?string
    {
        return $this->formMappings[$type][$name] ?? null;
    }

    public function getFormMappingCategories(): ?array
    {
        return array_keys($this->formMappings) ?? null;
    }
}
