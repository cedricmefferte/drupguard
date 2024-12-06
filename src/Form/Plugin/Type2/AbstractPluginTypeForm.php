<?php

namespace App\Form\Plugin\Type2;

use App\Registry\EntityFieldRegistry;
use App\Registry\PluginRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

abstract class AbstractPluginTypeForm extends AbstractType
{
    private string $pluginType;


    private EntityFieldRegistry $fieldRegistry;

    public function __construct(EntityFieldRegistry $fieldRegistry, string $pluginType)
    {
        $this->fieldRegistry = $fieldRegistry;
        $this->pluginType = $pluginType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fields = $this->fieldRegistry->getFieldMappingsByType($this->pluginType);

        foreach ($fields as $fieldName => $fieldConfig) {
            $builder->add($fieldName, $fieldConfig['type'], $fieldConfig['options'] ?? []);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $entityClass = $this->fieldRegistry->getEntityClassByType($this->pluginType);

        $resolver->setDefaults([
            'data_class' => $entityClass,
            'error_bubbling' => false,
        ]);
    }
}
