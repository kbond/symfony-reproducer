<?php

namespace App;

use App\ORM\HydrationTracker;
use App\ORM\TraceableEntityManager;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('doctrine.orm.entity_manager.abstract')
            ->setClass(TraceableEntityManager::class)
            ->setFactory([TraceableEntityManager::class, 'create'])
            ->addMethodCall('setHydrationTracker', [new Reference(HydrationTracker::class)])
        ;
    }
}
