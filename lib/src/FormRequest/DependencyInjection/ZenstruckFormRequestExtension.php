<?php

namespace Zenstruck\FormRequest\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Zenstruck\FormRequest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class ZenstruckFormRequestExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->register(FormRequest::class)
            ->addTag('container.service_subscriber')
            ->addMethodCall('setContainer', [new Reference(ContainerInterface::class)])
            ->setShared(false)
        ;
    }
}
