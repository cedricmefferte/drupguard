<?php

namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class DrupguardConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('drupguard');
        $rootNode = $treeBuilder->getRootNode();

        $this->addPluginsSection($rootNode);

        return $treeBuilder;
    }

    private function addPluginsSection(ArrayNodeDefinition $rootNode): void
    {

        $rootNode
            ->children()
                ->arrayNode('plugins')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('form_class')->isRequired()->end()
                            ->scalarNode('entity_class')->isRequired()->end()
                            ->scalarNode('repository_class')->isRequired()->end()
                            ->arrayNode('plugin_types')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('name')->isRequired()->end()
                                        ->scalarNode('entity_class')->isRequired()->end()
                                        ->scalarNode('repository_class')->isRequired()->end()
                                        ->scalarNode('form_class')->isRequired()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('entity_fields')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('fields')
                                ->useAttributeAsKey('name')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('type')->isRequired()->end()
                                        ->arrayNode('options')
                                           ->variablePrototype()->end()
                                    ->end()
                                ->end()
                             ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
