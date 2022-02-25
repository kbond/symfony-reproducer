<?php

namespace App;

use App\ORM\HydrationTracker;
use App\ORM\TraceableHydratorFactory;
use Doctrine\ORM\Internal\Hydration\DefaultHydratorFactory;
use Doctrine\ORM\Internal\Hydration\HydratorFactory;
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
        $container->register('doctrine.orm.default_hydrator_factory', DefaultHydratorFactory::class);

        $container->register('doctrine.orm.traceable_hydrator_factory', TraceableHydratorFactory::class)
            ->setDecoratedService('doctrine.orm.default_hydrator_factory')
            ->setArguments([new Reference('.inner'), new Reference(HydrationTracker::class)])
        ;

        $container->getDefinition('doctrine.orm.configuration')
            ->addMethodCall('setHydratorFactory', [new Reference('doctrine.orm.default_hydrator_factory')])
        ;
    }
}
