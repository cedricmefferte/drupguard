<?php

namespace App\Form\Plugin\Type2;

use App\Form\Plugin\PluginInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Registry\PluginRegistry;
abstract class AbstractPluginForm extends AbstractType implements PluginInterface
{
    protected PluginRegistry $pluginRegistry;
    protected string $pluginType;

    public function __construct(PluginRegistry $pluginRegistry, string $pluginType)
    {
        $this->pluginRegistry = $pluginRegistry;
        $this->pluginType = $pluginType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $plugins = $this->pluginRegistry->getPlugins($this->pluginType);
        $choices = array_combine(
            array_column($plugins, 'name'),
            array_column($plugins, 'form_class')
        );

        $builder->add('type', ChoiceType::class, [
            'placeholder' => 'Choose an option',
            'choices' => $choices,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
