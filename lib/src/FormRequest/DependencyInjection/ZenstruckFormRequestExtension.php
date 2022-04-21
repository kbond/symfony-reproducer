<?php

namespace Zenstruck\FormRequest\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Zenstruck\FormRequest;
use Zenstruck\FormRequest\EventListener\ValidationFailedSubscriber;
use Zenstruck\FormRequest\Validator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class ZenstruckFormRequestExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->register(Validator::class)
            ->addArgument(new Reference('validator'))
        ;

        $container->register('zenstruck_form_request.validation_failed_subscriber', ValidationFailedSubscriber::class)
            ->addTag('kernel.event_subscriber')
        ;

        $container->register(FormRequest::class)
            ->addTag('container.service_subscriber')
            ->addMethodCall('setContainer', [new Reference(ContainerInterface::class)])
            ->setShared(false)
        ;
    }
}
