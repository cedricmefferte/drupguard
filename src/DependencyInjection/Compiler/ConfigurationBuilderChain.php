<?php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

// TODO: define if necessary in plugin type resolving / processsing (ProjectAnalyzis with plugins configuration per example)
class ConfigurationBuilderChain implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var array */
    private $chain;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->chain = array();
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param mixed  $service
     * @param string $alias
     */
    public function addBuilder($service, $alias)
    {
        $this->chain[$alias] = $service;
    }

    /**
     * @param string $alias
     *
     * @return mixed
     */
    public function getBuilder($alias)
    {
        if (\array_key_exists($alias.'.configuration_builder', $this->chain)) {
            return $this->container->get($this->chain[$alias.'.configuration_builder']);
        }
        return;
    }
}
