<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use App\DependencyInjection\Compiler\PluginRegistryCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use App\DependencyInjection\DrupguardExtension;
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->registerExtension(new DrupguardExtension());
        $container->addCompilerPass(new PluginRegistryCompilerPass());
    }
}
